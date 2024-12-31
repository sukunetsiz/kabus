<?php

namespace App\Http\Controllers;

use App\Models\PgpKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class PgpKeyConfirmationController extends Controller
{
    private const CONFIRMATION_EXPIRY_MINUTES = 16;

    public function showConfirmationForm()
    {
        $user = Auth::user();
        $pgpKey = $user->pgpKey;

        Log::info('User ID: ' . $user->id);
        Log::info('PGP Key: ' . ($pgpKey ? 'Found' : 'Not Found'));

        if (!$pgpKey) {
            return redirect()->route('profile')->with('error', 'You need to add a PGP key before verification.');
        }

        if ($pgpKey->verified) {
            return redirect()->route('profile')->with('info', 'Your PGP key is already verified.');
        }

        Log::info('PGP Key ID: ' . $pgpKey->id);
        Log::info('PGP Key User ID: ' . $pgpKey->user_id);

        $message = 'kabus' . mt_rand(1000000000, 9999999999);
        $encryptedMessage = '';

        // Create a unique temporary directory
        $tempDir = sys_get_temp_dir() . '/gnupg_' . uniqid();
        mkdir($tempDir, 0700);

        try {
            // Set GNUPGHOME environment variable to the temporary directory
            putenv("GNUPGHOME=" . $tempDir);

            // Create new GnuPG object
            $gpg = new \gnupg();

            // Set error mode to throw exceptions
            $gpg->seterrormode(\gnupg::ERROR_EXCEPTION);

            // Import the public key
            $importInfo = $gpg->import($pgpKey->public_key);
            Log::info('Import info: ' . json_encode($importInfo));

            if (!empty($importInfo['fingerprint'])) {
                // Add encryption key
                $gpg->addencryptkey($importInfo['fingerprint']);

                // Encrypt the message
                $encryptedMessage = $gpg->encrypt($message);
                Log::info('Message encrypted successfully');
            } else {
                throw new Exception("Failed to import the public key. No fingerprint returned.");
            }
        } catch (Exception $e) {
            Log::error('Error in PGP process: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()->route('profile')->with('error', 'An error occurred while processing your PGP key. Please try again or contact support if the problem persists.');
        } finally {
            // Clean up the temporary directory
            $this->cleanupTempDir($tempDir);
        }

        // Set the confirmation message and its expiration time in the session
        $expirationTime = Carbon::now()->addMinutes(self::CONFIRMATION_EXPIRY_MINUTES);
        session([
            'pgp_confirmation_message' => $message,
            'pgp_confirmation_expiry' => $expirationTime
        ]);

        return view('confirm-pgp-key', compact('encryptedMessage', 'expirationTime'));
    }

    public function confirmKey(Request $request)
    {
        $user = Auth::user();
        $pgpKey = $user->pgpKey;

        if (!$pgpKey) {
            return redirect()->route('profile')->with('error', 'You need to add a PGP key before verification.');
        }

        if ($pgpKey->verified) {
            return redirect()->route('profile')->with('info', 'Your PGP key is already verified.');
        }

        $request->validate([
            'decrypted_message' => 'required|string',
        ]);

        $originalMessage = session('pgp_confirmation_message');
        $expirationTime = session('pgp_confirmation_expiry');
        $decryptedMessage = $request->input('decrypted_message');

        // Check if the confirmation has expired
        if (Carbon::now()->isAfter($expirationTime)) {
            return redirect()->route('pgp.confirm')->with('error', 'Verification process has timed out. Please try again.');
        }

        if ($decryptedMessage === $originalMessage) {
            try {
                $pgpKey->verified = true;
                $pgpKey->save();
                return redirect()->route('profile')->with('success', 'Your PGP key has been successfully verified.');
            } catch (QueryException $e) {
                Log::error('Error saving PGP key verification status: ' . $e->getMessage());
                return redirect()->route('profile')->with('error', 'An error occurred while verifying your PGP key. Please try again or contact support if the problem persists.');
            }
        } else {
            return back()->with('error', 'The decrypted message does not match. Please try again.');
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
