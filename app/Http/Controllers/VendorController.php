<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Storage;
use MoneroIntegrations\MoneroPhp\walletRPC;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Exceptions\NotReadableException;
use Exception;

class VendorController extends Controller
{
    protected $walletRPC;
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    /**
     * Create a new controller instance.
     */
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
        }
    }

    /**
     * Display the vendor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('vendor.index');
    }

    /**
     * Show the form for editing vendor appearance.
     *
     * @return \Illuminate\View\View
     */
    public function showAppearance()
    {
        $user = Auth::user();
        $vendorProfile = $user->vendorProfile ?? new VendorProfile();
        
        return view('vendor.appearance', compact('vendorProfile'));
    }

    /**
     * Update the vendor's appearance settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:8|max:800',
            'vacation_mode' => 'required|in:0,1',
        ], [
            'description.required' => 'A description is required.',
            'description.min' => 'Description must be at least 8 characters.',
            'description.max' => 'Description cannot exceed 800 characters.',
            'vacation_mode.in' => 'Invalid vacation mode value.'
        ]);

        $user = Auth::user();
        $vendorProfile = $user->vendorProfile ?? new VendorProfile();
        
        if (!$user->vendorProfile) {
            $vendorProfile->user_id = $user->id;
        }
        
        $vendorProfile->description = $request->description;
        $vendorProfile->vacation_mode = (bool) $request->vacation_mode;
        $vendorProfile->save();

        return redirect()->route('vendor.appearance')
            ->with('success', 'Vendor settings updated successfully.');
    }

    /**
     * Display the vendor's products.
     *
     * @return \Illuminate\View\View
     */
    public function myProducts()
    {
        $products = Product::where('user_id', Auth::id())
            ->select('id', 'name', 'type', 'slug')
            ->get();

        // Check advertisement status for each product
        foreach ($products as $product) {
            $product->is_advertised = Advertisement::isProductAdvertised($product->id);
        }

        return view('vendor.my-products', compact('products'));
    }

    /**
     * Delete a product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->delete();

        return redirect()->route('vendor.my-products')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(string $type)
    {
        if (!in_array($type, [Product::TYPE_CARGO, Product::TYPE_DIGITAL, Product::TYPE_DEADDROP])) {
            abort(404);
        }

        // Get all categories
        $categories = Category::with('children')->get();
        
        // Get measurement units for the dropdown
        $measurementUnits = Product::getMeasurementUnits();

        // Get countries from JSON file
        $countries = json_decode(file_get_contents(storage_path('app/country.json')), true);

        return view('vendor.products.create', compact('type', 'categories', 'measurementUnits', 'countries'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request, string $type)
    {
        if (!in_array($type, [Product::TYPE_CARGO, Product::TYPE_DIGITAL, Product::TYPE_DEADDROP])) {
            abort(404);
        }

        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'product_picture' => [
                    'nullable',
                    'file',
                    'max:800', // 800KB max size
                ],
                'additional_photos.*' => [
                    'nullable',
                    'file',
                    'max:800', // 800KB max size
                ],
                'stock_amount' => 'required|integer|min:0|max:999999',
                'measurement_unit' => [
                    'required',
                    Rule::in(array_keys(Product::getMeasurementUnits()))
                ],
                'ships_from' => [
                    'required',
                    'string',
                    Rule::in(json_decode(file_get_contents(storage_path('app/country.json')), true))
                ],
                'ships_to' => [
                    'required',
                    'string',
                    Rule::in(json_decode(file_get_contents(storage_path('app/country.json')), true))
                ],
            ]);

            // Process delivery options
            $deliveryOptions = collect($request->delivery_options ?? [])->map(function ($option) {
                return [
                    'description' => trim($option['description'] ?? ''),
                    'price' => is_numeric($option['price']) ? (float) $option['price'] : null
                ];
            })->filter(function ($option) {
                return !empty($option['description']) && is_numeric($option['price']);
            })->values()->all();

            // Get appropriate error messages based on product type
            $deliveryOptionName = $type === Product::TYPE_DEADDROP ? 'pickup window' : 'delivery';

            // Validate delivery options
            if (empty($deliveryOptions)) {
                throw ValidationException::withMessages([
                    'delivery_options' => ["At least one {$deliveryOptionName} option is required."]
                ]);
            }

            if (count($deliveryOptions) > 4) {
                throw ValidationException::withMessages([
                    'delivery_options' => ["No more than 4 {$deliveryOptionName} options are allowed."]
                ]);
            }

            // Validate each delivery option
            foreach ($deliveryOptions as $index => $option) {
                if (strlen($option['description']) > 255) {
                    throw ValidationException::withMessages([
                        "delivery_options.{$index}.description" => ["{$deliveryOptionName} description cannot exceed 255 characters."]
                    ]);
                }

                if ($option['price'] < 0) {
                    throw ValidationException::withMessages([
                        "delivery_options.{$index}.price" => ["{$deliveryOptionName} price cannot be negative."]
                    ]);
                }
            }

            // Process bulk options (optional)
            $bulkOptions = collect($request->bulk_options ?? [])->map(function ($option) {
                return [
                    'amount' => is_numeric($option['amount']) ? (float) $option['amount'] : null,
                    'price' => is_numeric($option['price']) ? (float) $option['price'] : null
                ];
            })->filter(function ($option) {
                return is_numeric($option['amount']) && is_numeric($option['price']);
            })->values()->all();

            // Validate bulk options
            if (!empty($bulkOptions)) {
                if (count($bulkOptions) > 4) {
                    throw ValidationException::withMessages([
                        'bulk_options' => ['No more than 4 bulk options are allowed.']
                    ]);
                }

                // Validate each bulk option
                foreach ($bulkOptions as $index => $option) {
                    if ($option['amount'] <= 0) {
                        throw ValidationException::withMessages([
                            "bulk_options.{$index}.amount" => ['Amount must be greater than zero.']
                        ]);
                    }

                    if ($option['price'] <= 0) {
                        throw ValidationException::withMessages([
                            "bulk_options.{$index}.price" => ['Price must be greater than zero.']
                        ]);
                    }
                }
            }

            // Handle product picture if uploaded
            $productPicture = 'default-product-picture.png';
            if ($request->hasFile('product_picture')) {
                $productPicture = $this->handleProductPictureUpload($request->file('product_picture'));
            }

            // Handle additional photos if uploaded
            $additionalPhotos = [];
            if ($request->hasFile('additional_photos')) {
                foreach ($request->file('additional_photos') as $index => $photo) {
                    if ($index >= 3) break; // Limit to 3 additional photos
                    try {
                        $additionalPhotos[] = $this->handleProductPictureUpload($photo);
                    } catch (Exception $e) {
                        Log::warning('Failed to upload additional photo: ' . $e->getMessage(), [
                            'user_id' => Auth::id(),
                            'photo_index' => $index
                        ]);
                        // Continue with other photos if one fails
                        continue;
                    }
                }
            }

            // Prepare product data
            $productData = [
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'active' => true,
                'product_picture' => $productPicture,
                'stock_amount' => $validated['stock_amount'],
                'measurement_unit' => $validated['measurement_unit'],
                'delivery_options' => $deliveryOptions,
                'bulk_options' => $bulkOptions,
                'ships_from' => $validated['ships_from'],
                'ships_to' => $validated['ships_to'],
                'additional_photos' => $additionalPhotos
            ];

            // Create the product based on type
            $product = match($type) {
                Product::TYPE_CARGO => Product::createCargo($productData),
                Product::TYPE_DIGITAL => Product::createDigital($productData),
                Product::TYPE_DEADDROP => Product::createDeadDrop($productData),
            };

            // Get appropriate success message
            $productTypeName = match($type) {
                Product::TYPE_CARGO => 'Cargo',
                Product::TYPE_DIGITAL => 'Digital',
                Product::TYPE_DEADDROP => 'Dead Drop',
            };

            return redirect()
                ->route('vendor.index')
                ->with('success', "{$productTypeName} product created successfully.");

        } catch (Exception $e) {
            Log::error("Failed to create {$type} product: " . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get all categories
        $categories = Category::with('children')->get();
        
        // Get measurement units for the dropdown
        $measurementUnits = Product::getMeasurementUnits();

        // Get countries from JSON file
        $countries = json_decode(file_get_contents(storage_path('app/country.json')), true);

        return view('vendor.products.edit', compact('product', 'categories', 'measurementUnits', 'countries'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate the request
            $validated = $request->validate([
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'stock_amount' => 'required|integer|min:0|max:999999',
                'measurement_unit' => [
                    'required',
                    Rule::in(array_keys(Product::getMeasurementUnits()))
                ],
                'ships_from' => [
                    'required',
                    'string',
                    Rule::in(json_decode(file_get_contents(storage_path('app/country.json')), true))
                ],
                'ships_to' => [
                    'required',
                    'string',
                    Rule::in(json_decode(file_get_contents(storage_path('app/country.json')), true))
                ],
            ]);

            // Process delivery options
            $deliveryOptions = collect($request->delivery_options ?? [])->map(function ($option) {
                return [
                    'description' => trim($option['description'] ?? ''),
                    'price' => is_numeric($option['price']) ? (float) $option['price'] : null
                ];
            })->filter(function ($option) {
                return !empty($option['description']) && is_numeric($option['price']);
            })->values()->all();

            // Get appropriate error messages based on product type
            $deliveryOptionName = $product->type === Product::TYPE_DEADDROP ? 'pickup window' : 'delivery';

            // Validate delivery options
            if (empty($deliveryOptions)) {
                throw ValidationException::withMessages([
                    'delivery_options' => ["At least one {$deliveryOptionName} option is required."]
                ]);
            }

            if (count($deliveryOptions) > 4) {
                throw ValidationException::withMessages([
                    'delivery_options' => ["No more than 4 {$deliveryOptionName} options are allowed."]
                ]);
            }

            // Validate each delivery option
            foreach ($deliveryOptions as $index => $option) {
                if (strlen($option['description']) > 255) {
                    throw ValidationException::withMessages([
                        "delivery_options.{$index}.description" => ["{$deliveryOptionName} description cannot exceed 255 characters."]
                    ]);
                }

                if ($option['price'] < 0) {
                    throw ValidationException::withMessages([
                        "delivery_options.{$index}.price" => ["{$deliveryOptionName} price cannot be negative."]
                    ]);
                }
            }

            // Process bulk options (optional)
            $bulkOptions = collect($request->bulk_options ?? [])->map(function ($option) {
                return [
                    'amount' => is_numeric($option['amount']) ? (float) $option['amount'] : null,
                    'price' => is_numeric($option['price']) ? (float) $option['price'] : null
                ];
            })->filter(function ($option) {
                return is_numeric($option['amount']) && is_numeric($option['price']);
            })->values()->all();

            // Validate bulk options
            if (!empty($bulkOptions)) {
                if (count($bulkOptions) > 4) {
                    throw ValidationException::withMessages([
                        'bulk_options' => ['No more than 4 bulk options are allowed.']
                    ]);
                }

                // Validate each bulk option
                foreach ($bulkOptions as $index => $option) {
                    if ($option['amount'] <= 0) {
                        throw ValidationException::withMessages([
                            "bulk_options.{$index}.amount" => ['Amount must be greater than zero.']
                        ]);
                    }

                    if ($option['price'] <= 0) {
                        throw ValidationException::withMessages([
                            "bulk_options.{$index}.price" => ['Price must be greater than zero.']
                        ]);
                    }
                }
            }

            // Update product data
            $product->update([
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'stock_amount' => $validated['stock_amount'],
                'measurement_unit' => $validated['measurement_unit'],
                'delivery_options' => $deliveryOptions,
                'bulk_options' => $bulkOptions,
                'ships_from' => $validated['ships_from'],
                'ships_to' => $validated['ships_to'],
            ]);

            // Get appropriate success message
            $productTypeName = match($product->type) {
                Product::TYPE_CARGO => 'Cargo',
                Product::TYPE_DIGITAL => 'Digital',
                Product::TYPE_DEADDROP => 'Dead Drop',
            };

            return redirect()
                ->route('vendor.my-products')
                ->with('success', "{$productTypeName} product updated successfully.");

        } catch (Exception $e) {
            Log::error("Failed to update product: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'product_id' => $product->id
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Handle the product picture upload.
     */
    private function handleProductPictureUpload($file)
    {
        try {
            // Verify file type using finfo
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file->getPathname());

            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                throw new Exception('Invalid file type. Allowed types are JPEG, PNG, GIF, and WebP.');
            }

            $extension = $this->getExtensionFromMimeType($mimeType);
            $filename = time() . '_' . Str::uuid() . '.' . $extension;

            // Create a new ImageManager instance
            $manager = new ImageManager(new GdDriver());

            // Resize the image
            $image = $manager->read($file)
                ->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // Encode the image based on its MIME type
            $encodedImage = $this->encodeImage($image, $mimeType);

            // Save the image to private storage
            if (!Storage::disk('private')->put('product_pictures/' . $filename, $encodedImage)) {
                throw new Exception('Failed to save product picture to storage');
            }

            return $filename;
        } catch (NotReadableException $e) {
            Log::error('Image processing failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            throw new Exception('Failed to process uploaded image. Please try a different image.');
        } catch (Exception $e) {
            Log::error('Product picture upload failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get file extension from MIME type.
     */
    private function getExtensionFromMimeType($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }

    /**
     * Encode image based on MIME type.
     */
    private function encodeImage($image, $mimeType)
    {
        switch ($mimeType) {
            case 'image/png':
                return $image->encode(new PngEncoder());
            case 'image/webp':
                return $image->encode(new WebpEncoder());
            case 'image/gif':
                return $image->encode(new GifEncoder());
            default:
                return $image->encode(new JpegEncoder(80));
        }
    }

    /**
     * Show the form for creating a new advertisement.
     */
    /**
     * Prepare advertisement slot data with pricing and availability.
     *
     * @return array
     */
    private function prepareAdvertisementSlots()
    {
        $basePrice = config('monero.advertisement_base_price');
        $slots = [];

        foreach (config('monero.advertisement_slot_multipliers') as $slot => $multiplier) {
            $price = $basePrice * $multiplier;
            $isAvailable = !\App\Models\Advertisement::where('slot_number', $slot)
                ->where('payment_completed', true)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->exists();

            $slots[] = [
                'number' => $slot,
                'price' => $price,
                'is_available' => $isAvailable
            ];
        }

        return $slots;
    }

    /**
     * Show the form for creating a new advertisement.
     */
    public function createAdvertisement(Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if product is already being advertised
        if (Advertisement::isProductAdvertised($product->id)) {
            return redirect()
                ->route('vendor.my-products')
                ->with('error', 'This product is already being advertised in another slot.');
        }

        $slots = $this->prepareAdvertisementSlots();
        return view('vendor.advertisement.create', compact('product', 'slots'));
    }

    /**
     * Store a new advertisement and initiate payment.
     */
    public function storeAdvertisement(Request $request, Product $product)
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if product is already being advertised
        if (Advertisement::isProductAdvertised($product->id)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['product' => 'This product is already being advertised in another slot.']);
        }

        $validated = $request->validate([
            'slot_number' => [
                'required',
                'integer',
                'min:1',
                'max:8',
                function ($attribute, $value, $fail) use ($request) {
                    $duration = (int) $request->duration_days;
                    if ($duration < 1) {
                        return; // Let the other validation rules handle this
                    }
                    if (!Advertisement::isSlotAvailable($value, now(), now()->addDays($duration))) {
                        $fail('This slot is currently occupied.');
                    }
                },
            ],
            'duration_days' => [
                'required',
                'integer',
                'min:' . config('monero.advertisement_min_duration', 1),
                'max:' . config('monero.advertisement_max_duration', 30),
            ],
        ]);

        try {
            // Calculate required amount
            $requiredAmount = Advertisement::calculateRequiredAmount(
                $validated['slot_number'],
                $validated['duration_days']
            );

            // Create Monero subaddress
            $result = $this->walletRPC->create_address(0, "Advertisement Payment " . Auth::id() . "_" . time());

            // Create advertisement record
            $advertisement = new Advertisement([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'slot_number' => $validated['slot_number'],
                'duration_days' => $validated['duration_days'],
                'payment_address' => $result['address'],
                'payment_address_index' => $result['address_index'],
                'required_amount' => $requiredAmount,
                'expires_at' => now()->addMinutes((int) config('monero.address_expiration_time')),
            ]);

            $advertisement->save();

            return redirect()->route('vendor.advertisement.payment', $advertisement->payment_identifier);

        } catch (\Exception $e) {
            Log::error('Failed to create advertisement: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create advertisement. Please try again.');
        }
    }

    /**
     * Show the advertisement payment page.
     */
    public function showAdvertisementPayment(string $identifier)
    {
        $advertisement = Advertisement::where('payment_identifier', $identifier)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if payment has expired
        if ($advertisement->isExpired()) {
            return redirect()
                ->route('vendor.my-products')
                ->with('error', 'Payment window has expired.');
        }

        try {
            // Check for new payments
            $transfers = $this->walletRPC->get_transfers([
                'in' => true,
                'pool' => true,
                'subaddr_indices' => [$advertisement->payment_address_index]
            ]);

            $totalReceived = 0;
            foreach (['in', 'pool'] as $type) {
                if (isset($transfers[$type])) {
                    foreach ($transfers[$type] as $transfer) {
                        $totalReceived += $transfer['amount'] / 1e12;
                    }
                }
            }

            // Update received amount
            $advertisement->total_received = $totalReceived;

            // Check if payment is completed
            if ($totalReceived >= $advertisement->required_amount && !$advertisement->payment_completed) {
                $advertisement->payment_completed = true;
                $advertisement->payment_completed_at = now();
                $advertisement->starts_at = now();
                $advertisement->ends_at = now()->addDays((int) $advertisement->duration_days);
            }

            $advertisement->save();

            // Generate QR code
            $qrCode = null;
            if (!$advertisement->payment_completed) {
                $qrCode = $this->generateQrCode($advertisement->payment_address);
            }

            return view('vendor.advertisement.payment', [
                'advertisement' => $advertisement,
                'qrCode' => $qrCode
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing advertisement payment: ' . $e->getMessage());
            return view('vendor.advertisement.payment', [
                'advertisement' => $advertisement,
                'qrCode' => null,
                'error' => 'Error checking payment status. Please try refreshing the page.'
            ]);
        }
    }

    /**
     * Generate a QR code for the given address.
     */
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
