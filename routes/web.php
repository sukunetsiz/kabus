<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferencesController;
use App\Http\Controllers\PgpKeyConfirmationController;
use App\Http\Controllers\Pgp2FAController;
use App\Http\Controllers\BecomeVendorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReturnAddressController;
use App\Http\Controllers\MonetaController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\RhombusCaptchaController;
use App\Http\Controllers\GuidesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VendorsController;
use App\Http\Controllers\AddCargoProductController;
use App\Http\Controllers\AddDigitalProductController;
use App\Http\Controllers\AddDeadDropProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\LoginThrottle;
use App\Http\Middleware\CheckUnverifiedPgpKey;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VendorMiddleware;
use App\Http\Middleware\VerifyRhombusCaptcha;

// Base route handled by the captcha controller which manages both auth and guest users
Route::get('/', [RhombusCaptchaController::class, 'show']);

// All other routes are protected by the CAPTCHA middleware
Route::middleware(VerifyRhombusCaptcha::class)->group(function () {
    // Verify captcha route
    Route::post('/verify-captcha', [RhombusCaptchaController::class, 'verify'])->name('rhombus-captcha.verify');

    Route::get('/pgp-key', function () {
        return response()->file(storage_path('app/public/pgp_key.txt'));
    })->name('pgp-key');

    Route::get('/canary', function () {
        return response()->file(storage_path('app/public/canary.txt'));
    })->name('canary');

    Route::middleware('guest')->group(function () {
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware(LoginThrottle::class);
        Route::get('/mnemonic/{token}', [AuthController::class, 'showMnemonic'])->name('show.mnemonic');

        // New routes for forgot password functionality using mnemonic
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'verifyMnemonic'])->name('password.verify');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

        // New routes for 2FA challenge
        Route::get('/2fa/challenge', [Pgp2FAController::class, 'showChallenge'])->name('pgp.2fa.challenge');
        Route::post('/2fa/verify', [Pgp2FAController::class, 'verifyChallenge'])->name('pgp.2fa.verify');

        // New route for banned users
        Route::get('/banned', [AuthController::class, 'showBanned'])->name('banned');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/home', [AuthController::class, 'home'])->name('home');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Product routes
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/product/picture/{filename}', [ProductController::class, 'showPicture'])->name('product.picture');
        
        // Wishlist routes
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
        Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
        Route::delete('/wishlist', [WishlistController::class, 'clearAll'])->name('wishlist.clear');
        
        // Guides routes
        Route::get('/guides', [GuidesController::class, 'index'])->name('guides.index');
        Route::get('/guides/keepassxc', [GuidesController::class, 'keepassxc'])->name('guides.keepassxc');
        Route::get('/guides/monero', [GuidesController::class, 'monero'])->name('guides.monero');
        Route::get('/guides/buying-monero', [GuidesController::class, 'buyingMonero'])->name('guides.buying-monero');
        Route::get('/guides/tor', [GuidesController::class, 'tor'])->name('guides.tor');

        // Settings routes
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.changePassword');
        Route::post('/settings/update-pgp-key', [SettingsController::class, 'updatePgpKey'])->name('settings.updatePgpKey');

        // New routes for messaging
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

        // Moneta Game route
        Route::get('/moneta', [MonetaController::class, 'index'])->name('moneta.index');

        // New route for rules
        Route::get('/rules', [RulesController::class, 'index'])->name('rules');

        // Updated route for dashboard
        Route::get('/dashboard/{username?}', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture'])->name('profile.delete_picture');
        Route::get('/profile/picture/{filename}', [ProfileController::class, 'getProfilePicture'])->name('profile.picture');

        // Route for references
        Route::get('/references', [ReferencesController::class, 'index'])->name('references.index');

        // New routes for PGP key confirmation
        Route::middleware(CheckUnverifiedPgpKey::class)->group(function () {
            Route::get('/pgp/confirm', [PgpKeyConfirmationController::class, 'showConfirmationForm'])->name('pgp.confirm');
            Route::post('/pgp/confirm', [PgpKeyConfirmationController::class, 'confirmKey'])->name('pgp.confirm.submit');
        });

        // New route for PGP-based 2FA settings
        Route::put('/pgp/2fa', [Pgp2FAController::class, 'updateSettings'])->name('pgp.2fa.update');

        // New routes for becoming a vendor
        Route::get('/become-vendor', [BecomeVendorController::class, 'index'])->name('become.vendor');
        Route::get('/become-vendor/payment', [BecomeVendorController::class, 'payment'])->name('become.payment');

        // New routes for return addresses
        Route::get('/return-addresses', [ReturnAddressController::class, 'index'])->name('return-addresses.index');
        Route::post('/return-addresses', [ReturnAddressController::class, 'store'])->name('return-addresses.store');
        Route::delete('/return-addresses/{returnAddress}', [ReturnAddressController::class, 'destroy'])->name('return-addresses.destroy');

        // Notification routes for all users
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/{notification}/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Vendor listing routes
        Route::get('/vendors', [VendorsController::class, 'index'])->name('vendors.index');
        Route::get('/vendors/{username}', [VendorsController::class, 'show'])->name('vendors.show');

        // Admin routes
        Route::middleware(AdminMiddleware::class)->group(function () {
            Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
            Route::get('/admin/canary', [AdminController::class, 'showUpdateCanary'])->name('admin.canary');
            Route::post('/admin/canary', [AdminController::class, 'updateCanary'])->name('admin.canary.post');
            
            // Admin logs management
            Route::get('/admin/logs', [AdminController::class, 'showLogs'])->name('admin.logs');
            Route::get('/admin/logs/error', [AdminController::class, 'showErrorLogs'])->name('admin.logs.error');
            Route::get('/admin/logs/warning', [AdminController::class, 'showWarningLogs'])->name('admin.logs.warning');
            Route::get('/admin/logs/info', [AdminController::class, 'showInfoLogs'])->name('admin.logs.info');
            Route::delete('/admin/logs/{type}', [AdminController::class, 'deleteLogs'])->name('admin.logs.delete');
            
            // Admin user management
            Route::get('/admin/users', [AdminController::class, 'userList'])->name('admin.users');
            Route::get('/admin/users/{user}', [AdminController::class, 'userDetails'])->name('admin.users.details');
            Route::put('/admin/users/{user}/roles', [AdminController::class, 'updateUserRoles'])->name('admin.users.update-roles');
            Route::post('/admin/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
            Route::post('/admin/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('admin.users.unban');

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
            Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
            Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
            Route::delete('/admin/categories/{category}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');
            Route::get('/admin/categories/list', [AdminController::class, 'listCategories'])->name('admin.categories.list');
            
            // Admin Products Management
            Route::get('/admin/all-products', [AdminController::class, 'allProducts'])->name('admin.all-products');
            Route::delete('/admin/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');
        });

        // Vendor routes
        Route::middleware(VendorMiddleware::class)->group(function () {
            Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');
            Route::get('/vendor/appearance', [VendorController::class, 'showAppearance'])->name('vendor.appearance');
            Route::post('/vendor/appearance', [VendorController::class, 'updateAppearance'])->name('vendor.appearance.update');
            
            // My Products routes
            Route::get('/vendor/my-products', [VendorController::class, 'myProducts'])->name('vendor.my-products');
            Route::delete('/vendor/products/{product}', [VendorController::class, 'destroy'])->name('vendor.products.destroy');
            
            // Add Cargo Product routes
            Route::get('/vendor/products/cargo/create', [AddCargoProductController::class, 'create'])->name('vendor.products.cargo.create');
            Route::post('/vendor/products/cargo', [AddCargoProductController::class, 'store'])->name('vendor.products.cargo.store');

            // Add Digital Product routes
            Route::get('/vendor/products/digital/create', [AddDigitalProductController::class, 'create'])->name('vendor.products.digital.create');
            Route::post('/vendor/products/digital', [AddDigitalProductController::class, 'store'])->name('vendor.products.digital.store');

            // Add Dead Drop Product routes
            Route::get('/vendor/products/deaddrop/create', [AddDeadDropProductController::class, 'create'])->name('vendor.products.deaddrop.create');
            Route::post('/vendor/products/deaddrop', [AddDeadDropProductController::class, 'store'])->name('vendor.products.deaddrop.store');
        });
    });
});

// Catch-all route for non-existing pages
Route::fallback(function () {
    // If user is authenticated, redirect to home
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->to('/');
});
