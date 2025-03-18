<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferencesController;
use App\Http\Controllers\BecomeVendorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReturnAddressController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\GuidesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorsController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\DisputesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\LoginThrottle;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VendorMiddleware;

// -----------------------------------------------------------------------------
// Base Route
// -----------------------------------------------------------------------------

// When users first enter the site, they are taken to the login page.
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// -----------------------------------------------------------------------------
// Public File Routes
// -----------------------------------------------------------------------------

Route::get('/pgp-key', function () {
    return response()->file(storage_path('app/public/pgp_key.txt'));
})->name('pgp-key');

Route::get('/canary', function () {
    return response()->file(storage_path('app/public/canary.txt'));
})->name('canary');

// -----------------------------------------------------------------------------
// Routes for Guest Users
// -----------------------------------------------------------------------------

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware(LoginThrottle::class);
    Route::get('/mnemonic/{token}', [AuthController::class, 'showMnemonic'])->name('show.mnemonic');

    // Password reset routes using mnemonic
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'verifyMnemonic'])->name('password.verify');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');

    // PGP 2FA challenge routes
    Route::get('/2fa/challenge', [AuthController::class, 'showPgp2FAChallenge'])->name('pgp.2fa.challenge');
    Route::post('/2fa/verify', [AuthController::class, 'verifyPgp2FAChallenge'])->name('pgp.2fa.verify');

    // New route for banned users
    Route::get('/banned', [AuthController::class, 'showBanned'])->name('banned');
});

// -----------------------------------------------------------------------------
// Routes for Authenticated Users
// -----------------------------------------------------------------------------

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/home', [HomeController::class, 'dismissPopup'])->name('popup.dismiss');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/product/picture/{filename}', [ProductController::class, 'showPicture'])->name('product.picture');
    
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/{cart}/message', [CartController::class, 'saveMessage'])->name('cart.message.save');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    
    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::delete('/wishlist', [WishlistController::class, 'clearAll'])->name('wishlist.clear');
    
    // Guides routes
    Route::get('/guides', [GuidesController::class, 'index'])->name('guides.index');
    Route::get('/guides/keepassxc', [GuidesController::class, 'keepassxc'])->name('guides.keepassxc');
    Route::get('/guides/monero', [GuidesController::class, 'monero'])->name('guides.monero');
    Route::get('/guides/tor', [GuidesController::class, 'tor'])->name('guides.tor');
    Route::get('/guides/kleopatra', [GuidesController::class, 'kleopatra'])->name('guides.kleopatra');

    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.changePassword');
    Route::post('/settings/update-pgp-key', [SettingsController::class, 'updatePgpKey'])->name('settings.updatePgpKey');
    Route::post('/settings/update-secret-phrase', [SettingsController::class, 'updateSecretPhrase'])->name('settings.updateSecretPhrase');

    // Messaging routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'startConversation'])->name('messages.start');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::delete('/messages/{conversation}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Support request routes for users
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{supportRequest:ticket_id}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{supportRequest:ticket_id}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Rules route
    Route::get('/rules', [RulesController::class, 'index'])->name('rules');

    // Dashboard route
    Route::get('/dashboard/{username?}', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture'])->name('profile.delete_picture');
    Route::get('/profile/picture/{filename}', [ProfileController::class, 'getProfilePicture'])->name('profile.picture');

    // References route
    Route::get('/references', [ReferencesController::class, 'index'])->name('references.index');

    // PGP key confirmation routes
    Route::get('/pgp/confirm', [ProfileController::class, 'showPgpConfirmationForm'])->name('pgp.confirm');
    Route::post('/pgp/confirm', [ProfileController::class, 'confirmPgpKey'])->name('pgp.confirm.submit');

    // PGP 2FA settings route
    Route::put('/pgp/2fa', [AuthController::class, 'updatePgp2FASettings'])->name('pgp.2fa.update');

    // Becoming a vendor routes
    Route::get('/become-vendor', [BecomeVendorController::class, 'index'])->name('become.vendor');
    Route::get('/become-vendor/payment', [BecomeVendorController::class, 'payment'])->name('become.payment');
    Route::get('/become-vendor/application', [BecomeVendorController::class, 'showApplication'])->name('become.vendor.application');
    Route::post('/become-vendor/application', [BecomeVendorController::class, 'submitApplication'])->name('become.vendor.submit-application');

    // Return addresses routes
    Route::get('/return-addresses', [ReturnAddressController::class, 'index'])->name('return-addresses.index');
    Route::post('/return-addresses', [ReturnAddressController::class, 'store'])->name('return-addresses.store');
    Route::delete('/return-addresses/{returnAddress}', [ReturnAddressController::class, 'destroy'])->name('return-addresses.destroy');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/{notification}/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Vendor listing routes
    Route::get('/vendors', [VendorsController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{username}', [VendorsController::class, 'show'])->name('vendors.show');
    
    // Order routes
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{uniqueUrl}', [OrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
    Route::post('/orders/{uniqueUrl}/mark-paid', [OrdersController::class, 'markAsPaid'])->name('orders.mark-paid');
    Route::post('/orders/{uniqueUrl}/mark-delivered', [OrdersController::class, 'markAsDelivered'])->name('orders.mark-delivered');
    Route::post('/orders/{uniqueUrl}/mark-completed', [OrdersController::class, 'markAsCompleted'])->name('orders.mark-completed');
    Route::post('/orders/{uniqueUrl}/mark-cancelled', [OrdersController::class, 'markAsCancelled'])->name('orders.mark-cancelled');
    Route::post('/orders/{uniqueUrl}/review/{orderItemId}', [OrdersController::class, 'submitReview'])->name('orders.submit-review');
    
    // Dispute routes
    Route::get('/disputes', [DisputesController::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{id}', [DisputesController::class, 'show'])->name('disputes.show');
    Route::post('/disputes/{uniqueUrl}', [DisputesController::class, 'store'])->name('disputes.store');
    Route::post('/disputes/{id}/message', [DisputesController::class, 'addMessage'])->name('disputes.add-message');

    // -------------------------------------------------------------------------
    // Admin Routes
    // -------------------------------------------------------------------------
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/canary', [AdminController::class, 'showUpdateCanary'])->name('admin.canary');
        Route::post('/admin/canary', [AdminController::class, 'updateCanary'])->name('admin.canary.post');
        
        // Admin statistics
        Route::get('/admin/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
        
        // Admin logs management
        Route::get('/admin/logs', [AdminController::class, 'showLogs'])->name('admin.logs');
        Route::get('/admin/logs/{type}', [AdminController::class, 'showLogsByType'])
            ->name('admin.logs.show')
            ->where('type', 'error|warning|info');
        Route::delete('/admin/logs/{type}', [AdminController::class, 'deleteLogs'])->name('admin.logs.delete');
        
        // Admin user management
        Route::get('/admin/users', [AdminController::class, 'userList'])->name('admin.users');
        Route::get('/admin/users/{user}', [AdminController::class, 'userDetails'])->name('admin.users.details');
        Route::put('/admin/users/{user}/roles', [AdminController::class, 'updateUserRoles'])->name('admin.users.update-roles');
        Route::post('/admin/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
        Route::post('/admin/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('admin.users.unban');
        
        // Admin dispute management
        Route::get('/admin/disputes', [DisputesController::class, 'adminIndex'])->name('admin.disputes.index');
        Route::get('/admin/disputes/{id}', [DisputesController::class, 'adminShow'])->name('admin.disputes.show');
        Route::post('/admin/disputes/{id}/vendor-prevails', [DisputesController::class, 'resolveVendorPrevails'])->name('admin.disputes.vendor-prevails');
        Route::post('/admin/disputes/{id}/buyer-prevails', [DisputesController::class, 'resolveBuyerPrevails'])->name('admin.disputes.buyer-prevails');

        // Admin support request management
        Route::get('/admin/support', [AdminController::class, 'supportRequests'])->name('admin.support.requests');
        Route::get('/admin/support/{supportRequest:ticket_id}', [AdminController::class, 'showSupportRequest'])->name('admin.support.show');
        Route::post('/admin/support/{supportRequest:ticket_id}/reply', [AdminController::class, 'replySupportRequest'])->name('admin.support.reply');
        Route::put('/admin/support/{supportRequest:ticket_id}/status', [AdminController::class, 'updateSupportStatus'])->name('admin.support.status');

        // Bulk Message routes
        Route::get('/admin/bulk-message', [AdminController::class, 'showBulkMessage'])->name('admin.bulk-message.create');
        Route::post('/admin/bulk-message', [AdminController::class, 'sendBulkMessage'])->name('admin.bulk-message.send');
        Route::get('/admin/bulk-message/list', [AdminController::class, 'listBulkMessages'])->name('admin.bulk-message.list');
        Route::delete('/admin/bulk-message/{notification}', [AdminController::class, 'deleteBulkMessage'])->name('admin.bulk-message.delete');

        // Category Management Routes
        // Vendor Application Management Routes
        Route::get('/admin/vendor-applications', [AdminController::class, 'vendorApplications'])->name('admin.vendor-applications.index');
        Route::get('/admin/vendor-applications/{application}', [AdminController::class, 'showVendorApplication'])->name('admin.vendor-applications.show');
        Route::post('/admin/vendor-applications/{application}/accept', [AdminController::class, 'acceptVendorApplication'])->name('admin.vendor-applications.accept');
        Route::post('/admin/vendor-applications/{application}/deny', [AdminController::class, 'denyVendorApplication'])->name('admin.vendor-applications.deny');

        Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
        Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
        Route::delete('/admin/categories/{category}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');
        Route::get('/admin/categories/list', [AdminController::class, 'listCategories'])->name('admin.categories.list');
        
        // Admin Pop-up Management
        Route::get('/admin/pop-up', [AdminController::class, 'popupIndex'])->name('admin.popup.index');
        Route::get('/admin/pop-up/create', [AdminController::class, 'popupCreate'])->name('admin.popup.create');
        Route::post('/admin/pop-up', [AdminController::class, 'popupStore'])->name('admin.popup.store');
        Route::post('/admin/pop-up/{popup}/activate', [AdminController::class, 'popupActivate'])->name('admin.popup.activate');
        Route::delete('/admin/pop-up/{popup}', [AdminController::class, 'popupDestroy'])->name('admin.popup.destroy');
        
        // Admin Products Management
        Route::get('/admin/all-products', [AdminController::class, 'allProducts'])->name('admin.all-products');
        Route::get('/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
        Route::patch('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
        Route::delete('/admin/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');
    });

    // -------------------------------------------------------------------------
    // Vendor Routes
    // -------------------------------------------------------------------------
    Route::middleware(VendorMiddleware::class)->group(function () {
        Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');
        Route::get('/vendor/appearance', [VendorController::class, 'showAppearance'])->name('vendor.appearance');
        Route::post('/vendor/appearance', [VendorController::class, 'updateAppearance'])->name('vendor.appearance.update');
        
        // My Products routes
        Route::get('/vendor/my-products', [VendorController::class, 'myProducts'])->name('vendor.my-products');
        Route::delete('/vendor/products/{product}', [VendorController::class, 'destroy'])->name('vendor.products.destroy');
        
        // Sales routes
        Route::get('/vendor/sales', [VendorController::class, 'sales'])->name('vendor.sales');
        Route::get('/vendor/sales/{uniqueUrl}', [VendorController::class, 'showSale'])->name('vendor.sales.show');
        Route::post('/vendor/sales/{uniqueUrl}/update-delivery-text', [VendorController::class, 'updateDeliveryText'])->name('vendor.sales.update-delivery-text');
        
        // Vendor dispute routes
        Route::get('/vendor/disputes', [DisputesController::class, 'vendorDisputes'])->name('vendor.disputes.index');
        Route::get('/vendor/disputes/{id}', [DisputesController::class, 'vendorShow'])->name('vendor.disputes.show');

        // Advertisement routes
        Route::get('/vendor/advertisement/rate-limit', [VendorController::class, 'showRateLimit'])
            ->name('vendor.advertisement.rate-limit');
        Route::get('/vendor/advertisement/create/{product}', [VendorController::class, 'createAdvertisement'])
            ->name('vendor.advertisement.create');
        Route::post('/vendor/advertisement/{product}', [VendorController::class, 'storeAdvertisement'])
            ->name('vendor.advertisement.store');
        Route::get('/vendor/advertisement/payment/{identifier}', [VendorController::class, 'showAdvertisementPayment'])
            ->name('vendor.advertisement.payment');
        
        // Product creation and editing routes
        Route::get('/vendor/products/{type}/create', [VendorController::class, 'create'])
            ->name('vendor.products.create')
            ->where('type', 'cargo|digital|deaddrop');
        Route::post('/vendor/products/{type}', [VendorController::class, 'store'])
            ->name('vendor.products.store')
            ->where('type', 'cargo|digital|deaddrop');
        Route::get('/vendor/products/{product}/edit', [VendorController::class, 'edit'])
            ->name('vendor.products.edit');
        Route::patch('/vendor/products/{product}', [VendorController::class, 'update'])
            ->name('vendor.products.update');
    });
});

// -----------------------------------------------------------------------------
// Fallback Route
// -----------------------------------------------------------------------------

Route::fallback(function () {
    // If user is authenticated, redirect to home; otherwise, to the login page.
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

