<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    public function showRegisterForm()
    {
        $captchaCode = $this->captchaService->generateCode();
        session(['captcha_code' => $captchaCode]);
        return view('auth.register', ['captchaCode' => $captchaCode]);
    }

    public function register(AuthRequest $request)
    {
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

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'mnemonic' => $mnemonic,  // Save mnemonic directly, it will be encrypted by the model
            'reference_id' => $referenceId,  // Save reference ID, it will be encrypted by the model
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

    public function login(AuthRequest $request)
    {
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
        $user = User::where('username', $credentials['username'])->first();

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

    public function home()
    {
        return view('home', ['username' => Auth::user()->username]);
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

    protected function generateMnemonic($numWords = 12)
    {
        if (!Storage::exists('wordlist.json')) {
            return false;
        }

        $words = json_decode(Storage::get('wordlist.json'), true);
        if (!is_array($words) || count($words) < 2048) {
            return false;
        }

        $wordCount = count($words);
        $systemEntropy = $this->getSystemEntropy();
        $indices = array_rand($words, $numWords);
        $mnemonic = [];

        foreach ($indices as $i => $index) {
            $entropyMix = random_bytes(32) . $systemEntropy . microtime(true) . getmypid();
            $randomIndex = ($index + hexdec(substr(hash('sha256', $entropyMix . $i), 0, 8))) % $wordCount;
            $mnemonic[] = $words[$randomIndex];
        }

        return implode(' ', $mnemonic);
    }

    protected function getSystemEntropy()
    {
        static $staticEntropy = null;
        if ($staticEntropy === null) {
            $staticEntropy = php_uname() . disk_free_space("/") . disk_total_space("/");
        }
        $entropy = $staticEntropy;
        $entropy .= memory_get_usage(true);
        $entropy .= microtime(true);
        $entropy .= getmypid();
        return hash('sha256', $entropy, true);
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