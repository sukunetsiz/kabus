<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MoneroIntegrations\MoneroPhp\walletRPC;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\VendorPayment;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BecomeVendorController extends Controller
{
    protected $walletRPC;

    public function __construct()
    {
        $config = config('monero');
        try {
            $this->walletRPC = new walletRPC(
                $config['host'],
                $config['port'],
                $config['ssl']
            );
        } catch (\Exception $e) {
            Log::error('Failed to initialize Monero RPC connection: ' . $e->getMessage());
            // You might want to set a flag or use a different approach to handle this error throughout the controller
        }
    }

    public function index()
    {
        return view('become.index');
    }

    public function payment(Request $request)
    {
        $user = $request->user();

        // Check if the user is already a vendor
        if ($user->isVendor()) {
            return view('become.payment', ['alreadyVendor' => true]);
        }

        try {
            $vendorPayment = $this->getCurrentVendorPayment($user);
            $qrCodeDataUri = $vendorPayment ? $this->generateQrCode($vendorPayment->address) : null;

            return view('become.payment', compact('vendorPayment', 'qrCodeDataUri'));
        } catch (\Exception $e) {
            Log::error('Error in payment process: ' . $e->getMessage());
            return view('become.payment', ['error' => 'An error occurred while processing your payment. Please try again later.']);
        }
    }

    private function getCurrentVendorPayment(User $user)
    {
        try {
            $vendorPayment = VendorPayment::where('user_id', $user->id)
                ->where('expires_at', '>', Carbon::now())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($vendorPayment) {
                $this->checkIncomingTransaction($vendorPayment);
                return $vendorPayment;
            } else {
                return $this->createVendorPayment($user);
            }
        } catch (\Exception $e) {
            Log::error('Error getting current vendor payment: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createVendorPayment(User $user)
    {
        try {
            $result = $this->walletRPC->create_address(0, "Vendor Payment " . $user->id . "_" . time());
            
            $vendorPayment = new VendorPayment([
                'address' => $result['address'],
                'address_index' => $result['address_index'],
                'user_id' => $user->id,
                'expires_at' => Carbon::now()->addMinutes((int)config('monero.address_expiration_time')),
            ]);
            $vendorPayment->save();

            Log::info('Created new vendor payment address for user ' . $user->id);
            return $vendorPayment;
        } catch (\Exception $e) {
            Log::error('Error creating Monero subaddress: ' . $e->getMessage());
            throw $e;
        }
    }

    private function checkIncomingTransaction(VendorPayment $vendorPayment)
    {
        try {
            $config = config('monero');
            $transfers = $this->walletRPC->get_transfers([
                'in' => true,
                'pool' => true,
                'subaddr_indices' => [$vendorPayment->address_index]
            ]);
            
            $totalReceived = 0;
            
            foreach (['in', 'pool'] as $type) {
                if (isset($transfers[$type])) {
                    foreach ($transfers[$type] as $transfer) {
                        if ($transfer['amount'] >= $config['vendor_payment_minimum_amount'] * 1e12) {
                            $totalReceived += $transfer['amount'] / 1e12;
                        }
                    }
                }
            }
            
            $vendorPayment->total_received = $totalReceived;
            $vendorPayment->save();
            
            if ($totalReceived >= $config['vendor_payment_required_amount']) {
                $this->upgradeToVendor($vendorPayment->user);
            }

            Log::info('Updated incoming transaction for user ' . $vendorPayment->user_id . '. Total received: ' . $totalReceived);
        } catch (\Exception $e) {
            Log::error('Error checking incoming Monero transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    private function upgradeToVendor(User $user)
    {
        try {
            $vendorRole = Role::where('name', 'vendor')->first();
            if ($vendorRole) {
                if (!$user->roles->contains($vendorRole->id)) {
                    $user->roles()->attach($vendorRole->id);
                    Log::info("User {$user->id} upgraded to Vendor role.");
                }
            } else {
                Log::error('Vendor role not found in the database.');
                throw new \Exception('Vendor role not found');
            }
        } catch (\Exception $e) {
            Log::error('Error upgrading user to vendor: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateQrCode($address)
    {
        try {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($address)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10)
                ->build();
            
            return $result->getDataUri();
        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            return null;
        }
    }
}
