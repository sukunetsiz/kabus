<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

class AddDeadDropProductController extends Controller
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    /**
     * Show the form for creating a new deaddrop product.
     */
    public function create()
    {
        // Get all categories
        $categories = Category::with('children')->get();
        
        // Get measurement units for the dropdown
        $measurementUnits = Product::getMeasurementUnits();

        return view('vendor.products.deaddrop.create', compact('categories', 'measurementUnits'));
    }

    /**
     * Store a newly created deaddrop product in storage.
     */
    public function store(Request $request)
    {
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
                'stock_amount' => 'required|integer|min:0|max:999999',
                'measurement_unit' => [
                    'required',
                    Rule::in(array_keys(Product::getMeasurementUnits()))
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

            // Validate delivery options
            if (empty($deliveryOptions)) {
                throw ValidationException::withMessages([
                    'delivery_options' => ['At least one pickup window option is required.']
                ]);
            }

            if (count($deliveryOptions) > 4) {
                throw ValidationException::withMessages([
                    'delivery_options' => ['No more than 4 pickup window options are allowed.']
                ]);
            }

            // Validate each delivery option
            foreach ($deliveryOptions as $index => $option) {
                if (strlen($option['description']) > 255) {
                    throw ValidationException::withMessages([
                        "delivery_options.{$index}.description" => ['Pickup window description cannot exceed 255 characters.']
                    ]);
                }

                if ($option['price'] < 0) {
                    throw ValidationException::withMessages([
                        "delivery_options.{$index}.price" => ['Pickup window fee cannot be negative.']
                    ]);
                }
            }

            // Handle product picture if uploaded
            $productPicture = 'default-product-picture.png';
            if ($request->hasFile('product_picture')) {
                $productPicture = $this->handleProductPictureUpload($request->file('product_picture'));
            }

            // Create the deaddrop product
            $product = Product::createDeadDrop([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'active' => true,
                'product_picture' => $productPicture,
                'stock_amount' => $validated['stock_amount'],
                'measurement_unit' => $validated['measurement_unit'],
                'delivery_options' => $deliveryOptions
            ]);

            return redirect()
                ->route('vendor.index')
                ->with('success', 'Dead Drop product created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create dead drop product: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
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
}