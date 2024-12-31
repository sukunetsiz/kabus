<?php

namespace App\Http\Controllers;

use App\Models\PgpKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Pgp2FAController extends Controller
{
    private const CONFIRMATION_EXPIRY_MINUTES = 16;

    public function showSettings()
    {
        $user = Auth::user();
        $pgpKey = $user->pgpKey;

        if (!$pgpKey || !$pgpKey->verified) {
            return redirect()->route('profile')->with('error', 'You need to add and verify a PGP key before enabling 2FA.');
        }

        return view('pgp-2fa-settings', compact('pgpKey'));
    }

    public function updateSettings(Request $request)
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

    public function showChallenge()
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

            $message = 'kabus' . Str::random(10);
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

    public function verifyChallenge(Request $request)
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

    private function isGnuPGAvailable()
    {
        return extension_loaded('gnupg');
    }

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
}
