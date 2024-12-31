<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductPictureController extends Controller
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    /**
     * Display the product picture.
     */
    public function show($filename)
    {
        try {
            // Check if the user is authenticated
            if (!Auth::check()) {
                abort(403, 'Unauthorized action.');
            }

            // If it's the default picture, serve it from the public directory
            if ($filename === 'default-product-picture.png') {
                return response()->file(public_path('images/default-product-picture.png'));
            }

            // Get the path to the image
            $path = 'product_pictures/' . $filename;

            // Check if the file exists
            if (!Storage::disk('private')->exists($path)) {
                throw new Exception('Product picture not found');
            }

            // Get the file content
            $file = Storage::disk('private')->get($path);

            // Verify file type using finfo
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($file);

            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                throw new Exception('Invalid file type');
            }

            // Create the response
            $response = Response::make($file, 200);
            $response->header("Content-Type", $mimeType);

            return $response;
        } catch (Exception $e) {
            Log::error('Failed to retrieve product picture: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'filename' => $filename
            ]);
            
            // If the picture is not found or invalid, return the default picture
            return response()->file(public_path('images/default-product-picture.png'));
        }
    }
}