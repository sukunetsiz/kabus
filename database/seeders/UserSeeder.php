<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to seed roles and users...');

        // Create admin and vendor roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $this->command->info('Admin and Vendor roles created successfully.');

        $userCount = 16;
        $successCount = 0;
        $errorCount = 0;

        for ($i = 1; $i <= $userCount; $i++) {
            try {
                $username = $this->generateValidUsername($i);
                $password = $this->generateValidPassword();
                $mnemonic = $this->generateMnemonic();
                $referenceId = $this->generateReferenceId();

                if ($mnemonic === false) {
                    throw new \Exception("Unable to generate mnemonic.");
                }

                $user = User::create([
                    'username' => $username,
                    'password' => Hash::make($password),
                    'mnemonic' => $mnemonic,
                    'reference_id' => $referenceId,
                ]);

                // Assign admin role to the first user
                if ($i === 1) {
                    $user->roles()->attach($adminRole);
                    $this->command->info("Admin user created successfully:");
                } 
                // Assign vendor role to the next 3 users
                elseif ($i >= 2 && $i <= 4) {
                    $user->roles()->attach($vendorRole);
                    $this->command->info("Vendor user created successfully:");
                }
                else {
                    $this->command->info("Regular user created successfully:");
                }

                $this->command->info("Username: {$username}");
                $this->command->info("Password: {$password}");
                $this->command->info("Mnemonic: {$mnemonic}");
                $this->command->info("Reference ID: {$referenceId}");
                $this->command->info($i === 1 ? "Role: Admin" : ($i >= 2 && $i <= 4 ? "Role: Vendor" : "Role: Regular User"));
                $this->command->info("---");

                $successCount++;
            } catch (\Exception $e) {
                $this->command->error("Error creating user{$i}: " . $e->getMessage());
                Log::error("Error seeding user{$i}: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->command->info("Seeding completed.");
        $this->command->info("Successfully created users: {$successCount}");
        $this->command->info("Failed to create users: {$errorCount}");
    }

/**
     * Generate a valid username based on the AuthController rules.
     */
    private function generateValidUsername($index): string
    {
        $baseUsername = 'user' . $index;
        $username = $baseUsername;
        
        // Ensure username is between 4 and 16 characters
        if (strlen($username) < 4) {
            $username .= Str::random(4 - strlen($username));
        } elseif (strlen($username) > 16) {
            $username = substr($username, 0, 16);
        }

        // Ensure username only contains letters and numbers
        $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);

        // Check if username already exists and modify if necessary
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . Str::random(1);
            $username = substr($username, 0, 16);
        }

        return $username;
    }

    /**
     * Generate a valid password based on the AuthController rules.
     */
    private function generateValidPassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specialChars = '#$%&@^`~.,:;"\'\/|_-<>*+!?=[](){}';

        $password = $lowercase[rand(0, strlen($lowercase) - 1)] . 
                    $uppercase[rand(0, strlen($uppercase) - 1)] . 
                    $numbers[rand(0, strlen($numbers) - 1)] . 
                    $specialChars[rand(0, strlen($specialChars) - 1)];

        for ($i = strlen($password); $i < 8; $i++) {
            $allChars = $lowercase . $uppercase . $numbers . $specialChars;
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Generate a mnemonic phrase.
     */
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

    /**
     * Get system entropy for mnemonic generation.
     */
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

    /**
     * Generate a unique reference ID.
     */
    protected function generateReferenceId(): string
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
