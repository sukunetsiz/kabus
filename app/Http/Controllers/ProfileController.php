<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Exceptions\NotReadableException;
use Exception;

class ProfileController extends Controller
{
    private $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile ?? $user->profile()->create();

        return view('profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'description' => [
                    'required',
                    'string',
                    'min:4',
                    'max:1024',
                    'regex:/^[\p{L}\p{N}\s\p{P}]+$/u'
                ],
                'profile_picture' => [
                    'nullable',
                    'file',
                    'max:800',
                ],
            ], [
                'description.regex' => 'Description can only contain letters, numbers, spaces, and punctuation marks.',
                'profile_picture.max' => 'Profile picture must not be larger than 800KB.',
            ]);

            $user = Auth::user();
            $profile = $user->profile ?? $user->profile()->create();

            $profile->description = Crypt::encryptString($request->description);

            if ($request->hasFile('profile_picture')) {
                $this->handleProfilePictureUpload($request->file('profile_picture'), $profile);
            }

            $profile->save();

            return redirect()->route('profile')->with('success', 'Profile successfully updated.');
        } catch (Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()->route('profile')->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    public function deleteProfilePicture()
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;

            if ($profile && $profile->profile_picture) {
                if (!Storage::disk('private')->delete('profile_pictures/' . $profile->profile_picture)) {
                    throw new Exception('Failed to delete profile picture from storage');
                }
                $profile->profile_picture = null;
                $profile->save();
            }

            return redirect()->route('profile')->with('success', 'Profile picture successfully deleted.');
        } catch (Exception $e) {
            Log::error('Profile picture deletion failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return redirect()->route('profile')->with('error', 'An error occurred while deleting your profile picture. Please try again.');
        }
    }

    private function handleProfilePictureUpload($file, $profile)
    {
        try {
            // Verify file type using finfo
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file->getPathname());

            if (!in_array($mimeType, $this->allowedMimeTypes)) {
                throw new Exception('Invalid file type. Allowed types are JPEG, PNG, GIF, and WebP.');
            }

            // Delete old profile picture if exists
            if ($profile->profile_picture) {
                if (!Storage::disk('private')->delete('profile_pictures/' . $profile->profile_picture)) {
                    throw new Exception('Failed to delete old profile picture from storage');
                }
            }

            $extension = $this->getExtensionFromMimeType($mimeType);
            $filename = time() . '_' . Str::uuid() . '.' . $extension;

            // Create a new ImageManager instance
            $manager = new ImageManager(new GdDriver());

            // Resize the image
            $image = $manager->read($file)
                ->resize(160, 160, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // Encode the image based on its MIME type
            $encodedImage = $this->encodeImage($image, $mimeType);

            // Save the image to private storage
            if (!Storage::disk('private')->put('profile_pictures/' . $filename, $encodedImage)) {
                throw new Exception('Failed to save profile picture to storage');
            }

            $profile->profile_picture = $filename;
        } catch (NotReadableException $e) {
            Log::error('Image processing failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            throw new Exception('Could not process the uploaded image. Please try a different image.');
        } catch (Exception $e) {
            Log::error('Profile picture upload failed: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            throw new Exception($e->getMessage());
        }
    }

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

    public function getProfilePicture($filename)
    {
        try {
            // Check if the user is authenticated
            if (!Auth::check()) {
                abort(403, 'Unauthorized action.');
            }

            // Get the path to the image
            $path = 'profile_pictures/' . $filename;

            // Check if the file exists
            if (!Storage::disk('private')->exists($path)) {
                throw new Exception('Profile picture not found');
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
            Log::error('Failed to retrieve profile picture: ' . $e->getMessage(), ['user_id' => Auth::id(), 'filename' => $filename]);
            abort(404, 'Profile picture not found');
        }
    }
}
