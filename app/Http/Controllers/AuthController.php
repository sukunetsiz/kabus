<?php

namespace App\Http\Controllers;

use App\Models\PgpKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use FurqanSiddiqui\BIP39\BIP39;
use FurqanSiddiqui\BIP39\Language\English;

class AuthController extends Controller
{
    private const CONFIRMATION_EXPIRY_MINUTES = 16;
    protected $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    /**
     * Show PGP 2FA settings page.
     */
    public function showPgp2FASettings()
    {
        $user = Auth::user();
        $pgpKey = $user->pgpKey;

        if (!$pgpKey || !$pgpKey->verified) {
            return redirect()->route('profile')->with('error', 'You need to add and verify a PGP key before enabling 2FA.');
        }

        return view('pgp-2fa-settings', compact('pgpKey'));
    }

    /**
     * Update PGP 2FA settings.
     */
    public function updatePgp2FASettings(Request $request)
    {
        $user = Auth::user();
        $pgpKey = $user->pgpKey;

        if (!$pgpKey || !$pgpKey->verified) {
            return redirect()->route('profile')->with('error', 'You need to add and verify a PGP key before enabling 2FA.');
        }

        try {
            $two_fa_enabled = $request->has('two_fa_enabled') && $request->two_fa_enabled == '1';

            $pgpKey->two_fa_enabled = $two_fa_enabled;
            $pgpKey->save();

            $status = $two_fa_enabled ? 'enabled' : 'disabled';
            Log::info("User {$user->id} has {$status} PGP-based 2FA.");
            return redirect()->route('settings')->with('success', "PGP-based 2FA has been {$status}.");
        } catch (\Exception $e) {
            Log::error("Error updating 2FA settings for user {$user->id}: " . $e->getMessage());
            return redirect()->route('settings')->with('error', 'An error occurred while updating your 2FA settings. Please try again.');
        }
    }

    /**
     * Show PGP 2FA challenge page.
     */
    public function showPgp2FAChallenge()
    {
        $userId = Session::get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Invalid 2FA session.');
        }

        try {
            $user = User::findOrFail($userId);
            $pgpKey = $user->pgpKey;

            if (!$pgpKey || !$pgpKey->verified || !$pgpKey->two_fa_enabled) {
                Log::warning("Invalid 2FA configuration for user {$userId}");
                return redirect()->route('login')->with('error', 'Invalid 2FA configuration.');
            }

            if (!$this->isGnuPGAvailable()) {
                Log::error("GnuPG is not available for 2FA challenge generation");
                return redirect()->route('login')->with('error', 'Could not generate 2FA challenge. Please contact support team.');
            }

            $message = 'PGP-' . Str::random(10) . '-KEY';
            $encryptedMessage = $this->encryptMessage($message, $pgpKey->public_key);

            if ($encryptedMessage === false) {
                Log::error("Failed to encrypt 2FA challenge for user {$userId}");
                return redirect()->route('login')->with('error', 'Could not generate 2FA challenge. Please try again or contact support team.');
            }

            $expirationTime = now()->addMinutes(self::CONFIRMATION_EXPIRY_MINUTES);
            Session::put('2fa_message', $message);
            Session::put('2fa_expiry', $expirationTime);

            return view('auth.2fa-challenge', compact('encryptedMessage', 'expirationTime'));
        } catch (\Exception $e) {
            Log::error("Error in 2FA challenge process for user {$userId}: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'An error occurred during 2FA process. Please try again or contact support team if the problem persists.');
        }
    }

    /**
     * Verify PGP 2FA challenge response.
     */
    public function verifyPgp2FAChallenge(Request $request)
    {
        $userId = Session::get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Invalid 2FA session.');
        }

        try {
            $user = User::findOrFail($userId);

            $request->validate([
                'decrypted_message' => 'required|string',
            ]);

            $originalMessage = Session::get('2fa_message');
            $expirationTime = Session::get('2fa_expiry');
            $decryptedMessage = $request->input('decrypted_message');

            if (now()->isAfter($expirationTime)) {
                Log::info("2FA verification timed out for user {$userId}");
                return redirect()->route('login')->with('error', '2FA verification has expired. Please try again.');
            }

            if (hash_equals($originalMessage, $decryptedMessage)) {
                Auth::login($user);
                $request->session()->regenerate();
                $user->update(['last_login' => now()]);

                Session::forget(['2fa_user_id', '2fa_message', '2fa_expiry']);

                Log::info("User {$userId} successfully completed 2FA verification");
                return redirect()->intended('home');
            } else {
                Log::warning("Failed 2FA attempt for user {$userId}");
                return back()->with('error', 'Decrypted message does not match. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error("Error in 2FA verification process for user {$userId}: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'An error occurred during 2FA verification. Please try again or contact support team if the problem persists.');
        }
    }

    /**
     * Check if GnuPG extension is available.
     */
    private function isGnuPGAvailable()
    {
        return extension_loaded('gnupg');
    }

    /**
     * Encrypt a message using PGP public key.
     */
    private function encryptMessage($message, $publicKey)
    {
        $tempDir = sys_get_temp_dir() . '/gnupg_' . uniqid();
        mkdir($tempDir, 0700);

        try {
            putenv("GNUPGHOME=" . $tempDir);
            $gpg = new \gnupg();
            $gpg->seterrormode(\gnupg::ERROR_EXCEPTION);

            $importInfo = $gpg->import($publicKey);
            if (empty($importInfo['fingerprint'])) {
                throw new \Exception("Failed to import the public key. No fingerprint returned.");
            }

            $gpg->addencryptkey($importInfo['fingerprint']);
            return $gpg->encrypt($message);
        } catch (\Exception $e) {
            Log::error("Error encrypting 2FA message: " . $e->getMessage());
            return false;
        } finally {
            $this->cleanupTempDir($tempDir);
        }
    }

    /**
     * Clean up temporary GPG directory.
     */
    private function cleanupTempDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->cleanupTempDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function showRegisterForm()
    {
        $captchaCode = $this->captchaService->generateCode();
        session(['captcha_code' => $captchaCode]);
        return view('auth.register', ['captchaCode' => $captchaCode]);
    }

    protected function getValidationRules(string $type = 'login'): array
    {
        $captchaLength = strlen(session('captcha_code', ''));
        
        $rules = [
            'username' => [
                'required',
                'string',
                'min:4',
                'max:16',
                'regex:/^[a-zA-Z0-9]+$/'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:40',
            ],
            'captcha' => ['required', 'string', "size:$captchaLength"],
        ];

        if ($type === 'register') {
            // Add registration-specific rules
            $rules['username'][] = 'unique:users';
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'max:40',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]])[A-Za-z\d#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]]{8,}$/',
            ];
            $rules['password_confirmation'] = ['required', 'string'];

            // Reference code validation
            $referenceCodeRules = [
                'string',
                'size:16',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        $validReference = User::all()->contains(function ($user) use ($value) {
                            return $user->reference_id === $value;
                        });

                        if (!$validReference) {
                            $fail('Invalid reference number.');
                        }
                    }
                },
            ];

            // Add required rule if configured
            if (config('marketplace.require_reference_code', true)) {
                array_unshift($referenceCodeRules, 'required');
            } else {
                array_unshift($referenceCodeRules, 'nullable');
            }

            $rules['reference_code'] = $referenceCodeRules;
        }

        return $rules;
    }

    protected function getValidationMessages(): array
    {
        $captchaLength = strlen(session('captcha_code', ''));
        return [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 4 characters.',
            'username.max' => 'Username cannot exceed 16 characters.',
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'Username can only contain letters and numbers.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot exceed 40 characters.',
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'reference_code.required' => 'Reference number is required.',
            'reference_code.size' => 'Reference number must be exactly 16 characters.',
            'captcha.required' => 'CAPTCHA is required.',
            'captcha.size' => "CAPTCHA must be exactly $captchaLength characters.",
        ];
    }

    public function register(Request $request)
    {
        // Validate request
        $validated = $request->validate(
            $this->getValidationRules('register'),
            $this->getValidationMessages()
        );

        // Validate CAPTCHA
        $captchaCode = session('captcha_code');
        if (!hash_equals(strtoupper($captchaCode), strtoupper($request->captcha))) {
            return back()->withErrors([
                'captcha' => 'Invalid CAPTCHA code.',
            ])->withInput($request->except('password', 'password_confirmation', 'reference_code'));
        }

        // Clear CAPTCHA from session
        session()->forget('captcha_code');

        $mnemonic = $this->generateMnemonic();
        if ($mnemonic === false) {
            return back()->withErrors([
                'mnemonic' => 'Failed to generate mnemonic. Please try again later.',
            ])->withInput($request->except('password', 'password_confirmation', 'reference_code'));
        }

        $referenceId = $this->generateReferenceId();

        // Find referrer if reference code was provided
        $referrerId = null;
        if ($request->has('reference_code')) {
            $referrer = User::all()->first(function ($user) use ($request) {
                return $user->reference_id === $request->reference_code;
            });
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'mnemonic' => $mnemonic,  // Save mnemonic directly, it will be encrypted by the model
            'reference_id' => $referenceId,  // Save reference ID, it will be encrypted by the model
            'referred_by' => $referrerId,
        ]);

        // Generate a unique token for accessing the mnemonic
        $mnemonicToken = Str::random(64);
        $expiresAt = now()->addMinutes(30);
        session([
            'mnemonic_token' => $mnemonicToken,
            'mnemonic_token_expires_at' => $expiresAt,
            $mnemonicToken => Crypt::encryptString($mnemonic)  // Encrypt the mnemonic before storing in session
        ]);

        return redirect()->route('show.mnemonic', ['token' => $mnemonicToken]);
    }

    public function showLoginForm()
    {
        $captchaCode = $this->captchaService->generateCode();
        session(['captcha_code' => $captchaCode]);
        return view('auth.login', ['captchaCode' => $captchaCode]);
    }

    public function login(Request $request)
    {
        // Validate request
        $validated = $request->validate(
            $this->getValidationRules('login'),
            $this->getValidationMessages()
        );

        // Validate CAPTCHA
        $captchaCode = session('captcha_code');
        if (!hash_equals(strtoupper($captchaCode), strtoupper($request->captcha))) {
            return back()->withErrors([
                'captcha' => 'Invalid CAPTCHA code.',
            ])->onlyInput('username');
        }

        // Clear CAPTCHA from session
        session()->forget('captcha_code');

        $credentials = $request->only('username', 'password');
        $user = User::whereRaw('BINARY username = ?', [$credentials['username']])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        if ($user->isBanned()) {
            return redirect()->route('banned')->with('banned_user', $user);
        }

        if ($user->pgpKey && $user->pgpKey->verified && $user->pgpKey->two_fa_enabled) {
            // Store user ID in session for 2FA process
            Session::put('2fa_user_id', $user->id);
            return redirect()->route('pgp.2fa.challenge');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login' => now()]);
        return redirect()->intended('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Show the form for requesting a password reset.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Verify mnemonic and generate password reset token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyMnemonic(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|min:4|max:16|regex:/^[a-zA-Z0-9]+$/',
            'mnemonic' => 'required|string|min:40|max:512',
        ], [
            'username.required' => 'Please enter your username.',
            'username.min' => 'Username must be at least 4 characters.',
            'username.max' => 'Username cannot be longer than 16 characters.',
            'username.regex' => 'Username can only contain letters and numbers.',
            'mnemonic.required' => 'Please enter your mnemonic phrase.',
            'mnemonic.min' => 'Mnemonic phrase must be at least 40 characters.',
            'mnemonic.max' => 'Mnemonic phrase cannot be longer than 512 characters.',
        ]);

        $user = User::whereRaw('BINARY username = ?', [$request->username])->first();

        if (!$user || !$this->verifyMnemonicPhrase($request->mnemonic, $user->mnemonic)) {
            return back()->withErrors([
                'error' => 'Username or mnemonic phrase is incorrect.',
            ])->withInput($request->only('username'));
        }

        $token = Str::random(64);
        $user->update([
            'password_reset_token' => Hash::make($token),
            'password_reset_expires_at' => now()->addMinutes(60),
        ]);

        return redirect()->route('password.reset', ['token' => $token])
            ->with('status', 'Please reset your password.');
    }

    /**
     * Verify the provided mnemonic phrase against the stored one.
     *
     * @param  string  $providedMnemonic
     * @param  string  $storedMnemonic
     * @return bool
     */
    private function verifyMnemonicPhrase($providedMnemonic, $storedMnemonic)
    {
        return hash_equals($storedMnemonic, $providedMnemonic);
    }

    /**
     * Show the form for resetting the password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:40',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]])[A-Za-z\d#$%&@^`~.,:;"\'\/|_\-<>*+!?={\[\]()\}\]]{8,}$/',
            ],
            'password_confirmation' => ['required', 'string'],
        ], [
            'token.required' => 'Reset token is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password cannot exceed 40 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'password_confirmation.required' => 'Please confirm your password.',
        ]);

        $user = User::where('password_reset_expires_at', '>', now())->get()
            ->first(function ($user) use ($request) {
                return Hash::check($request->token, $user->password_reset_token);
            });

        if (!$user) {
            return back()->withErrors(['error' => 'This password reset token is invalid or has expired.']);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return redirect()->route('login')
            ->with('status', 'Your password has been successfully reset. You can now login with your new password.');
    }

    public function showMnemonic(Request $request, $token)
    {
        if (!session()->has('mnemonic_token') || 
            session('mnemonic_token') !== $token || 
            !session()->has($token) ||
            now()->isAfter(session('mnemonic_token_expires_at'))) {
            return redirect()->route('login')->with('error', 'Invalid or expired mnemonic token.');
        }

        $mnemonic = Crypt::decryptString(session($token));  // Decrypt the mnemonic from session
        
        // Clear the mnemonic and token from the session
        session()->forget(['mnemonic_token', 'mnemonic_token_expires_at', $token]);

        return view('auth.mnemonic', ['mnemonic' => $mnemonic]);
    }

    public function showBanned()
    {
        if (!session()->has('banned_user')) {
            return redirect()->route('login');
        }

        $bannedUser = session('banned_user');
        return view('auth.banned', ['bannedUser' => $bannedUser]);
    }

    /**
     * Generate a secure mnemonic phrase using BIP39.
     *
     * @return string|false
     */
    protected function generateMnemonic()
    {
        try {
            $mnemonic = BIP39::fromRandom(
                English::getInstance(),
                wordCount: 12
            );
            return implode(' ', $mnemonic->words);
        } catch (\Exception $e) {
            Log::error('Failed to generate mnemonic: ' . $e->getMessage());
            return false;
        }
    }

    protected function generateReferenceId()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $referenceId = '';
        $characterCount = strlen($characters);
        
        for ($i = 0; $i < 16; $i++) {
            $referenceId .= $characters[random_int(0, $characterCount - 1)];
        }
        
        // Ensure there are exactly 8 letters and 8 digits
        $letters = preg_replace('/[^A-Z]/', '', $referenceId);
        $digits = preg_replace('/[^0-9]/', '', $referenceId);
        
        while (strlen($letters) < 8) {
            $letters .= $characters[random_int(0, 25)];
        }
        while (strlen($digits) < 8) {
            $digits .= $characters[random_int(26, 35)];
        }
        
        // Trim excess characters if necessary
        $letters = substr($letters, 0, 8);
        $digits = substr($digits, 0, 8);
        
        // Combine and shuffle
        $referenceId = str_shuffle($letters . $digits);
        
        return $referenceId;
    }
}
