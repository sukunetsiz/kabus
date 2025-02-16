<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use App\Models\BannedUser;
use App\Models\SupportRequest;
use App\Models\Notification;
use App\Models\Category;
use App\Models\Product;
use App\Models\Popup;
use App\Models\VendorPayment;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Exceptions\NotReadableException;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function showUpdateCanary()
    {
        $currentCanary = Storage::get('public/canary.txt');
        return view('admin.canary', compact('currentCanary'));
    }

    public function updateCanary(Request $request)
    {
        $request->validate([
            'canary' => 'required|string|max:3200',
        ]);

        Storage::put('public/canary.txt', $request->canary);

        return redirect()->route('admin.canary')->with('success', 'Canary updated successfully.');
    }

    public function showLogs()
    {
        return view('admin.logs.index');
    }

    private function getFilteredLogs($logTypes)
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(\n|\z)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $logType = strtolower($match[3]);
                if (in_array($logType, $logTypes)) {
                    $logs[] = [
                        'datetime' => $match[1],
                        'type' => $match[3],
                        'message' => $match[4],
                    ];
                }
            }
        }

        return array_reverse($logs);
    }

    public function showLogsByType($type)
    {
        $logTypes = match($type) {
            'error' => ['error', 'critical', 'alert', 'emergency'],
            'warning' => ['warning', 'notice'],
            'info' => ['info', 'debug'],
            default => abort(404)
        };

        $logs = $this->getFilteredLogs($logTypes);
        return view('admin.logs.show', compact('logs', 'type'));
    }

    public function deleteLogs($type)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(\n|\z)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            $newContent = '';
            foreach ($matches as $match) {
                $logType = strtolower($match[3]);
                if ($type === 'error' && !in_array($logType, ['error', 'critical', 'alert', 'emergency'])) {
                    $newContent .= $match[0];
                } elseif ($type === 'warning' && !in_array($logType, ['warning', 'notice'])) {
                    $newContent .= $match[0];
                } elseif ($type === 'info' && !in_array($logType, ['info', 'debug'])) {
                    $newContent .= $match[0];
                }
            }

            File::put($logPath, $newContent);
        }

        return redirect()->route('admin.logs')->with('success', ucfirst($type) . ' logs deleted successfully.');
    }

    public function userList()
    {
        $users = User::orderBy('username')->paginate(32);
        return view('admin.users.list', compact('users'));
    }

    public function userDetails(User $user)
    {
        $user->load('referrer');
        return view('admin.users.details', compact('user'));
    }

    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'in:admin,vendor',
        ]);

        $roles = $request->input('roles', []);

        // Sync the user's roles
        $roleIds = Role::whereIn('name', $roles)->pluck('id');
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.details', $user)
            ->with('success', 'User roles updated successfully.');
    }

    public function banUser(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
        ]);

        $bannedUntil = now()->addDays((int)$request->duration);

        BannedUser::updateOrCreate(
            ['user_id' => $user->id],
            [
                'reason' => $request->reason,
                'banned_until' => $bannedUntil,
            ]
        );

        // Log the ban action
        Log::info("User banned: ID {$user->id} banned until {$bannedUntil} for reason: {$request->reason}");

        return redirect()->route('admin.users.details', $user)
            ->with('success', 'User banned successfully.');
    }

    public function unbanUser(User $user)
    {
        $user->bannedUser()->delete();

        // Log the unban action
        Log::info("User unbanned: ID {$user->id}");

        return redirect()->route('admin.users.details', $user)
            ->with('success', 'User unbanned successfully.');
    }

    public function supportRequests()
    {
        $requests = SupportRequest::mainRequests()
            ->with(['user', 'latestMessage'])
            ->orderBy('created_at', 'desc')
            ->paginate(16);

        return view('admin.support.list', compact('requests'));
    }

    public function showSupportRequest(SupportRequest $supportRequest)
    {
        // Ensure we're viewing a main request
        if (!$supportRequest->isMainRequest()) {
            return redirect()->route('admin.support.requests')
                ->with('error', 'Invalid support request.');
        }

        $messages = $supportRequest->messages()->with('user')->get();
        
        return view('admin.support.show', compact('supportRequest', 'messages'));
    }

    public function replySupportRequest(Request $request, SupportRequest $supportRequest)
    {
        // Ensure we're replying to a main request
        if (!$supportRequest->isMainRequest()) {
            return redirect()->route('admin.support.requests')
                ->with('error', 'Invalid support request.');
        }

        // Add status validation check
        if ($supportRequest->status === 'closed') {
            return redirect()->route('admin.support.show', $supportRequest->ticket_id)
                ->with('error', 'Cannot reply to a closed ticket. Please update the ticket status to open or in progress first.');
        }

        $request->validate([
            'message' => 'required|string|max:5000'
        ]);

        // Create the admin reply as a child record
        $supportRequest->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin_reply' => true
        ]);

        // Create a notification for the user
        $notification = Notification::create([
            'title' => 'Support Request Update',
            'message' => "An admin has replied to your support request: \"{$supportRequest->title}\"",
            'type' => 'support'
        ]);

        // Send notification only to the support request's owner
        $notification->users()->attach($supportRequest->user_id);

        // Update support request status if needed
        if ($supportRequest->status === 'open') {
            $supportRequest->update(['status' => 'in_progress']);
        }

        return redirect()->route('admin.support.show', $supportRequest->ticket_id)
            ->with('success', 'Reply sent successfully.');
    }

    public function updateSupportStatus(Request $request, SupportRequest $supportRequest)
    {
        // Ensure we're updating a main request
        if (!$supportRequest->isMainRequest()) {
            return redirect()->route('admin.support.requests')
                ->with('error', 'Invalid support request.');
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,closed'
        ]);

        $oldStatus = $supportRequest->status;
        $supportRequest->update(['status' => $request->status]);

        // Only log when ticket is closed
        if ($request->status === 'closed') {
            Log::info("Support ticket {$supportRequest->ticket_id} closed by admin {$request->user()->id}");
        }

        return redirect()->route('admin.support.show', $supportRequest->ticket_id)
            ->with('success', 'Status updated successfully.');
    }

    // Bulk Messaging Methods
    public function showBulkMessage()
    {
        $roles = Role::all();
        return view('admin.bulk-message.create', compact('roles'));
    }

    public function sendBulkMessage(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|min:3|max:255',
                'message' => 'required|string|min:10|max:5000',
                'target_role' => 'nullable|string|in:admin,vendor',
            ]);

            // Strip any malicious HTML but keep basic formatting
            $sanitizedMessage = strip_tags($request->message, '<p><br><strong><em><ul><li><ol>');
            $sanitizedTitle = strip_tags($request->title);

            // Verify target role exists if specified
            if ($request->target_role) {
                $roleExists = Role::where('name', $request->target_role)->exists();
                if (!$roleExists) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Specified user group not found.');
                }
            }

            $notification = Notification::create([
                'title' => $sanitizedTitle,
                'message' => $sanitizedMessage,
                'target_role' => $request->target_role,
                'type' => 'bulk',
            ]);

            $notification->sendToTargetUsers();

            Log::info("Bulk message sent by admin {$request->user()->id} to " . 
                     ($request->target_role ?? 'all users'));

            return redirect()->route('admin.bulk-message.list')
                ->with('success', 'Bulk message sent successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending bulk message: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while sending the message. Please try again later.');
        }
    }

    public function listBulkMessages()
    {
        try {
            $notifications = Notification::where('type', 'bulk')
                ->orderBy('created_at', 'desc')
                ->withCount('users')
                ->paginate(16);

            // Add translated role names to each notification
            foreach ($notifications as $notification) {
                if ($notification->target_role) {
                    $roleTranslations = [
                        'vendor' => 'Vendor',
                        'admin' => 'Administrator'
                    ];
                    $notification->translated_role = $roleTranslations[$notification->target_role] ?? ucfirst($notification->target_role);
                }
            }

            return view('admin.bulk-message.list', compact('notifications'));
        } catch (\Exception $e) {
            Log::error('Error listing bulk messages: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while listing messages. Please try again later.');
        }
    }

    public function deleteBulkMessage(Notification $notification)
    {
        try {
            $notification->delete();
            return redirect()->route('admin.bulk-message.list')
                ->with('success', 'Bulk message deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting bulk message: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the message. Please try again later.');
        }
    }

    /**
     * Display a listing of popups.
     */
    public function popupIndex()
    {
        $popups = Popup::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pop-up.list', compact('popups'));
    }

    /**
     * Show the form for creating a new popup.
     */
    public function popupCreate()
    {
        return view('admin.pop-up.create');
    }

    /**
     * Store a newly created popup in storage.
     */
    public function popupStore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
                'active' => 'boolean'
            ]);

            $popup = Popup::create([
                'title' => $request->title,
                'message' => $request->message,
                'active' => $request->has('active')
            ]);

            Log::info("Pop-up created by admin {$request->user()->id}");

            return redirect()->route('admin.popup.index')
                ->with('success', 'Pop-up created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating pop-up: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating pop-up. Please try again.');
        }
    }

    /**
     * Activate a specific popup.
     */
    public function popupActivate(Popup $popup)
    {
        try {
            $popup->update(['active' => true]);
            Log::info("Pop-up {$popup->id} activated by admin " . auth()->id());

            return redirect()->route('admin.popup.index')
                ->with('success', 'Pop-up activated successfully.');
        } catch (\Exception $e) {
            Log::error('Error activating pop-up: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error activating pop-up. Please try again.');
        }
    }

    /**
     * Remove the specified popup from storage.
     */
    public function popupDestroy(Popup $popup)
    {
        try {
            $popup->delete();
            Log::info("Pop-up deleted by admin: {$popup->id}");

            return redirect()->route('admin.popup.index')
                ->with('success', 'Pop-up deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting pop-up: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting pop-up. Please try again.');
        }
    }

    // Category Management Methods
    public function categories()
    {
        $mainCategories = Category::mainCategories();
        return view('admin.categories', compact('mainCategories'));
    }

    public function storeCategory(Request $request)
    {
        try {
            $request->validate(Category::validationRules());

            $category = Category::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id
            ]);

            Log::info("Category created: {$category->getFormattedName()} by admin {$request->user()->id}");

            return redirect()->route('admin.categories')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating category. Please try again.');
        }
    }

    public function deleteCategory(Category $category)
    {
        try {
            $categoryName = $category->getFormattedName();
            
            // Begin transaction to ensure all operations complete successfully
            \DB::beginTransaction();
            
            $totalProductsCount = 0;
            $deletedCategoriesCount = 0;
            $categoryIds = [];
            $productsToDelete = [];

            // Function to recursively collect category IDs and products
            $collectCategoryInfo = function ($cat) use (&$collectCategoryInfo, &$totalProductsCount, &$categoryIds, &$productsToDelete) {
                // Get all products (including soft-deleted ones) and store them
                $products = $cat->products()->withTrashed()->get();
                $totalProductsCount += $products->count();
                $productsToDelete = array_merge($productsToDelete, $products->all());
                
                $categoryIds[] = $cat->id;

                // Recursively process child categories
                $cat->children()->get()->each(function ($child) use ($collectCategoryInfo) {
                    $collectCategoryInfo($child);
                });
            };

            // First collect all category IDs and products
            $collectCategoryInfo($category);

            // Delete all collected products
            foreach ($productsToDelete as $product) {
                $product->forceDelete();
            }

            // Delete categories from bottom up (children first)
            Category::whereIn('id', $categoryIds)
                ->orderBy('id', 'desc')
                ->get()
                ->each(function ($cat) use (&$deletedCategoriesCount) {
                    $cat->delete();
                    $deletedCategoriesCount++;
                });
            
            \DB::commit();

            // Log the deletion with counts
            $logMessage = "Category tree deleted: {$categoryName} by admin " . auth()->id() . 
                         ". {$totalProductsCount} products and {$deletedCategoriesCount} categories were permanently deleted.";
            Log::info($logMessage);

            return redirect()->route('admin.categories')
                ->with('success', "Category tree deleted successfully. {$totalProductsCount} products and " . 
                       ($deletedCategoriesCount - 1) . " subcategories were permanently deleted.");
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error deleting category: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting category. Please try again.');
        }
    }

    public function listCategories(Request $request)
    {
        // Only allow AJAX requests
        if (!$request->ajax()) {
            abort(404);
        }

        try {
            $categories = Category::with('parent')->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->getFormattedName()
                ];
            });

            return response()->json($categories);
        } catch (\Exception $e) {
            Log::error('Error listing categories: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching categories'], 500);
        }
    }

    /**
     * Display all products for admin.
     *
     * @return \Illuminate\View\View
     */
    public function allProducts(Request $request)
    {
        try {
            // Validate inputs
            $validated = $request->validate([
                'search' => ['nullable', 'string', 'min:1', 'max:100'],
                'vendor' => ['nullable', 'string', 'min:1', 'max:50'],
                'type' => ['nullable', Rule::in([Product::TYPE_DIGITAL, Product::TYPE_CARGO, Product::TYPE_DEADDROP])],
                'category' => ['nullable', 'integer', 'exists:categories,id'],
                'sort_price' => ['nullable', Rule::in(['asc', 'desc'])],
            ]);

            // Get only filled parameters
            $filters = collect($request->only(['search', 'vendor', 'type', 'category', 'sort_price']))
                ->filter(function ($value) {
                    return $value !== null && $value !== '';
                })
                ->toArray();

            $query = Product::with('user')
                ->select('products.*');

            // Apply search filters
            if (isset($filters['search'])) {
                $searchTerm = strip_tags($filters['search']);
                $query->where('name', 'like', '%' . addcslashes($searchTerm, '%_') . '%');
            }

            if (isset($filters['vendor'])) {
                $vendorTerm = strip_tags($filters['vendor']);
                $query->whereHas('user', function ($q) use ($vendorTerm) {
                    $q->where('username', 'like', '%' . addcslashes($vendorTerm, '%_') . '%');
                });
            }

            if (isset($filters['type'])) {
                $query->ofType($filters['type']);
            }

            if (isset($filters['category'])) {
                $query->where('category_id', (int) $filters['category']);
            }

            // Apply price sorting
            if (isset($filters['sort_price'])) {
                $query->orderBy('price', $filters['sort_price']);
            } else {
                $query->latest();
            }

            // Get paginated results
            $products = $query->paginate(32)->withQueryString();
            $categories = Category::select('id', 'name')->get();

            return view('admin.all-products.list', [
                'products' => $products,
                'categories' => $categories,
                'currentType' => $filters['type'] ?? null,
                'filters' => $filters
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all products: ' . $e->getMessage());
            return redirect()->route('admin.index')
                ->with('error', 'Error fetching products. Please try again.');
        }
    }

    /**
     * Delete a product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyProduct(Product $product)
    {
        try {
            $product->delete();
            
            // Log the deletion
            Log::info("Product deleted by admin: {$product->id}");

            return redirect()->route('admin.all-products')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->route('admin.all-products')
                ->with('error', 'Error deleting product. Please try again.');
        }
    }

    /**
     * Show the form for editing a product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function editProduct(Product $product)
    {
        try {
            // Get all categories
            $categories = Category::with('children')->get();
            
            // Get measurement units for the dropdown
            $measurementUnits = Product::getMeasurementUnits();

            // Get countries from JSON file
            $countries = json_decode(file_get_contents(storage_path('app/country.json')), true);

            return view('admin.all-products.edit', compact('product', 'categories', 'measurementUnits', 'countries'));
        } catch (\Exception $e) {
            Log::error('Error showing product edit form: ' . $e->getMessage());
            return redirect()->route('admin.all-products')
                ->with('error', 'Error loading product edit form. Please try again.');
        }
    }

    /**
     * Update the specified product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProduct(Request $request, Product $product)
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

            // Handle photo deletion requests
            if ($request->has('delete_main_photo') && $product->product_picture !== 'default-product-picture.png') {
                Storage::disk('private')->delete('product_pictures/' . $product->product_picture);
                $product->product_picture = 'default-product-picture.png';
            }

            if ($request->has('delete_additional_photo')) {
                $index = (int) $request->delete_additional_photo;
                if (isset($product->additional_photos[$index])) {
                    Storage::disk('private')->delete('product_pictures/' . $product->additional_photos[$index]);
                    $additionalPhotos = $product->additional_photos;
                    unset($additionalPhotos[$index]);
                    $product->additional_photos = array_values($additionalPhotos);
                }
            }

            // Handle new product picture if uploaded
            if ($request->hasFile('product_picture')) {
                // Delete old picture if it's not the default
                if ($product->product_picture !== 'default-product-picture.png') {
                    Storage::disk('private')->delete('product_pictures/' . $product->product_picture);
                }
                $product->product_picture = $this->handleProductPictureUpload($request->file('product_picture'));
            }

            // Handle additional photos if uploaded
            if ($request->hasFile('additional_photos')) {
                $currentCount = count($product->additional_photos ?? []);
                foreach ($request->file('additional_photos') as $index => $photo) {
                    if ($currentCount + $index >= 3) break; // Limit to 3 additional photos
                    try {
                        $product->additional_photos = array_merge(
                            $product->additional_photos ?? [],
                            [$this->handleProductPictureUpload($photo)]
                        );
                    } catch (Exception $e) {
                        Log::warning('Failed to upload additional photo: ' . $e->getMessage(), [
                            'product_id' => $product->id,
                            'photo_index' => $index
                        ]);
                        continue;
                    }
                }
            }

            // Update product data
            $product->update([
                'name' => $validated['name'],
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

            Log::info("Product updated by admin: {$product->id}");

            return redirect()
                ->route('admin.all-products')
                ->with('success', "{$productTypeName} product updated successfully.");

        } catch (Exception $e) {
            Log::error("Failed to update product: " . $e->getMessage(), [
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
    // Vendor Application Management Methods
    public function vendorApplications()
    {
        $applications = VendorPayment::whereNotNull('application_status')
            ->with('user')
            ->orderBy('application_submitted_at', 'desc')
            ->paginate(20);

        return view('admin.vendor-applications.list', compact('applications'));
    }

    public function showVendorApplication(VendorPayment $application, Request $request)
    {
        if (!$application->application_status) {
            return redirect()->route('admin.vendor-applications.list')
                ->with('error', 'Invalid application.');
        }

        // Check if this is an image request
        if ($request->has('image')) {
            $filename = $request->query('image');
            $images = json_decode($application->application_images, true) ?? [];
            
            // Verify the requested image belongs to this application
            if (!in_array($filename, $images)) {
                abort(404);
            }

            try {
                $path = 'vendor_application_pictures/' . $filename;
                
                if (!Storage::disk('private')->exists($path)) {
                    abort(404);
                }

                return response()->file(Storage::disk('private')->path($path));
            } catch (\Exception $e) {
                Log::error('Error serving vendor application image: ' . $e->getMessage());
                abort(404);
            }
        }

        return view('admin.vendor-applications.show', compact('application'));
    }

    public function acceptVendorApplication(VendorPayment $application)
    {
        if ($application->application_status !== 'waiting') {
            return redirect()->route('admin.vendor-applications.show', $application)
                ->with('error', 'This application has already been processed.');
        }

        try {
            \DB::beginTransaction();

            // Update application status
            $application->update([
                'application_status' => 'accepted',
                'admin_response_at' => now()
            ]);

            // Assign vendor role
            $vendorRole = Role::where('name', 'vendor')->firstOrFail();
            $application->user->roles()->syncWithoutDetaching([$vendorRole->id]);

            \DB::commit();

            Log::info("Vendor application accepted for user {$application->user_id}");

            return redirect()->route('admin.vendor-applications.show', $application)
                ->with('success', 'Application accepted successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error accepting vendor application: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while processing the application.');
        }
    }

    public function denyVendorApplication(VendorPayment $application)
    {
        if ($application->application_status !== 'waiting') {
            return redirect()->route('admin.vendor-applications.show', $application)
                ->with('error', 'This application has already been processed.');
        }

        try {
            \DB::beginTransaction();

            // Get refund percentage from config
            $refundPercentage = config('monero.vendor_payment_refund_percentage');
            if ($refundPercentage <= 0) {
                throw new \Exception('Refund percentage must be greater than zero');
            }

            // Calculate refund amount
            $refundAmount = $application->total_received * ($refundPercentage / 100);

            // Get random return address
            $returnAddress = $application->user->returnAddresses()
                ->inRandomOrder()
                ->first();

            if (!$returnAddress) {
                throw new \Exception('No return address found for user');
            }

            // First update application status
            $application->update([
                'application_status' => 'denied',
                'admin_response_at' => now()
            ]);

            try {
                // Initialize WalletRPC with increased timeout
                $config = config('monero');
                $walletRPC = new \MoneroIntegrations\MoneroPhp\walletRPC(
                    $config['host'],
                    $config['port'],
                    $config['ssl'],
                    30000  // 30 second timeout
                );

                // Process refund
                $result = $walletRPC->transfer([
                    'address' => $returnAddress->monero_address,
                    'amount' => $refundAmount,
                    'priority' => 1
                ]);

                // Update refund details only if transfer successful
                $application->update([
                    'refund_amount' => $refundAmount,
                    'refund_address' => $returnAddress->monero_address
                ]);

                $refundMessage = "Refund of {$refundAmount} XMR has been processed.";
            } catch (\Exception $e) {
                Log::error('Error processing refund: ' . $e->getMessage());
                $refundMessage = "Application denied but refund failed. Please process refund manually.";
                
                // Still update refund details for manual processing
                $application->update([
                    'refund_amount' => $refundAmount,
                    'refund_address' => $returnAddress->monero_address
                ]);
            }

            \DB::commit();

            Log::info("Vendor application denied for user {$application->user_id}. Refund processed: {$refundAmount} XMR to address {$returnAddress->monero_address}");

            return redirect()->route('admin.vendor-applications.show', $application)
                ->with('success', "Application denied successfully. " . $refundMessage);

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error denying vendor application: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while processing the application and refund.');
        }
    }

    private function handleProductPictureUpload($file)
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
                throw new Exception('Invalid file type. Allowed types are JPEG, PNG, GIF, and WebP.');
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

            // Save the image to private storage
            if (!Storage::disk('private')->put('product_pictures/' . $filename, $encodedImage)) {
                throw new Exception('Failed to save product picture to storage');
            }

            return $filename;
        } catch (NotReadableException $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            throw new Exception('Failed to process uploaded image. Please try a different image.');
        } catch (Exception $e) {
            Log::error('Product picture upload failed: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
