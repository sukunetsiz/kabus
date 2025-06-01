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
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Exceptions\NotReadableException;

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
            // Optionally, handle the error more gracefully here.
        }
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $hasPgpVerified = false;
        $hasMoneroAddress = false;

        if ($user->pgpKey) {
            $hasPgpVerified = $user->pgpKey->verified;
        }

        // Check if the user has at least one Monero return address.
        $hasMoneroAddress = $user->returnAddresses()->exists();

        // Get the latest vendor payment
        $vendorPayment = VendorPayment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('become-vendor.index', compact('hasPgpVerified', 'hasMoneroAddress', 'vendorPayment'));
    }

    public function payment(Request $request)
    {
        $user = $request->user();

        // Determine verification statuses.
        $hasPgpVerified = false;
        $hasMoneroAddress = false;

        if ($user->pgpKey) {
            $hasPgpVerified = $user->pgpKey->verified;
        }

        $hasMoneroAddress = $user->returnAddresses()->exists();

        // Check if user has a processed application
        $existingPayment = VendorPayment::where('user_id', $user->id)
            ->whereNotNull('application_status')
            ->first();

        if ($existingPayment) {
            return redirect()->route('become.vendor')
                ->with('info', 'You already have a processed vendor application.');
        }

        // If the user is already a vendor, pass the verification variables.
        if ($user->isVendor()) {
            return view('become-vendor.payment', [
                'alreadyVendor'   => true,
                'hasPgpVerified'  => $hasPgpVerified,
                'hasMoneroAddress'=> $hasMoneroAddress
            ]);
        }

        try {
            $vendorPayment = $this->getCurrentVendorPayment($user);
            $qrCodeDataUri = $vendorPayment ? $this->generateQrCode($vendorPayment->address) : null;

            return view('become-vendor.payment', [
                'vendorPayment'   => $vendorPayment,
                'qrCodeDataUri'   => $qrCodeDataUri,
                'hasPgpVerified'  => $hasPgpVerified,
                'hasMoneroAddress'=> $hasMoneroAddress
            ]);
        } catch (\Exception $e) {
            Log::error('Error in payment process: ' . $e->getMessage());
            return view('become-vendor.payment', [
                'error'           => 'An error occurred while processing your payment. Please try again later.',
                'hasPgpVerified'  => $hasPgpVerified,
                'hasMoneroAddress'=> $hasMoneroAddress
            ]);
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
                'address'       => $result['address'],
                'address_index' => $result['address_index'],
                'user_id'       => $user->id,
                'expires_at'    => Carbon::now()->addMinutes((int) config('monero.address_expiration_time')),
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
                'in'             => true,
                'pool'           => true,
                'subaddr_indices'=> [$vendorPayment->address_index]
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
            
            if ($totalReceived >= $config['vendor_payment_required_amount'] && !$vendorPayment->payment_completed) {
                $vendorPayment->payment_completed = true;
                $vendorPayment->save();
            }

            Log::info('Updated incoming transaction for user ' . $vendorPayment->user_id . '. Total received: ' . $totalReceived);
        } catch (\Exception $e) {
            Log::error('Error checking incoming Monero transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    public function showApplication()
    {
        // Check if user has a processed application
        $processedApplication = VendorPayment::where('user_id', auth()->id())
            ->whereNotNull('application_status')
            ->first();

        if ($processedApplication) {
            return redirect()->route('become.vendor')
                ->with('info', 'You already have a processed vendor application.');
        }

        $vendorPayment = VendorPayment::where('user_id', auth()->id())
            ->where('payment_completed', true)
            ->whereNull('application_status')
            ->first();

        if (!$vendorPayment) {
            return redirect()->route('become.vendor')
                ->with('error', 'You must complete the payment before submitting an application.');
        }

        return view('become-vendor.application', compact('vendorPayment'));
    }

    public function submitApplication(Request $request)
    {
        // Check if user has a processed application
        $processedApplication = VendorPayment::where('user_id', auth()->id())
            ->whereNotNull('application_status')
            ->first();

        if ($processedApplication) {
            return redirect()->route('become.vendor')
                ->with('info', 'You already have a processed vendor application.');
        }

        $vendorPayment = VendorPayment::where('user_id', auth()->id())
            ->where('payment_completed', true)
            ->whereNull('application_status')
            ->first();

        if (!$vendorPayment) {
            return redirect()->route('become.vendor')
                ->with('error', 'You must complete the payment before submitting an application.');
        }

        $request->validate([
            'application_text' => 'required|string|min:80|max:4000',
            'product_images' => [
                'required',
                'array',
                'min:1',
                'max:4'  // Maximum 4 images
            ],
            'product_images.*' => [
                'required',
                'file',
                'image',
                'max:800', // 800KB max size
                'mimes:jpeg,png,gif,webp'
            ]
        ]);

        try {
            $images = [];
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $image) {
                    try {
                        $images[] = $this->handleApplicationPictureUpload($image);
                    } catch (\Exception $e) {
                        // Clean up any images that were successfully uploaded
                        foreach ($images as $uploadedImage) {
                            Storage::disk('private')->delete('vendor_application_pictures/' . $uploadedImage);
                        }
                        Log::error('Failed to upload application image: ' . $e->getMessage());
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Failed to upload images. Please try again.');
                    }
                }
            }

            try {
                $vendorPayment->update([
                    'application_text' => $request->application_text,
                    'application_images' => json_encode($images),
                    'application_status' => 'waiting',
                    'application_submitted_at' => now()
                ]);
            } catch (\Exception $e) {
                // Clean up uploaded images if the update fails
                foreach ($images as $image) {
                    Storage::disk('private')->delete('vendor_application_pictures/' . $image);
                }
                throw $e;
            }

            Log::info("Vendor application submitted for user {$vendorPayment->user_id}");

            return redirect()->route('become.vendor')
                ->with('success', 'Your application has been submitted successfully and is now under review.');

        } catch (\Exception $e) {
            Log::error('Error submitting vendor application: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your application. Please try again.');
        }
    }

    private function handleApplicationPictureUpload($file)
    {
        try {
            // Verify file type using finfo
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file->getPathname());

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp'
            ];

            if (!in_array($mimeType, $allowedMimeTypes)) {
                throw new \Exception('Invalid file type. Allowed types are JPEG, PNG, GIF, and WebP.');
            }

            $extension = match($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg'
            };

            $filename = time() . '_' . \Str::uuid() . '.' . $extension;

            // Create a new ImageManager instance
            $manager = new ImageManager(new GdDriver());

            // Resize the image
            $image = $manager->read($file)
                ->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // Encode the image based on its MIME type
            $encodedImage = match($mimeType) {
                'image/png' => $image->encode(new PngEncoder()),
                'image/webp' => $image->encode(new WebpEncoder()),
                'image/gif' => $image->encode(new GifEncoder()),
                default => $image->encode(new JpegEncoder(80))
            };

            // Save the image to private storage in vendor_application_pictures directory
            if (!Storage::disk('private')->put('vendor_application_pictures/' . $filename, $encodedImage)) {
                throw new \Exception('Failed to save application picture to storage');
            }

            return $filename;
        } catch (NotReadableException $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            throw new \Exception('Failed to process uploaded image. Please try a different image.');
        } catch (\Exception $e) {
            Log::error('Application picture upload failed: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
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