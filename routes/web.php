<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DriverVerificationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DriverOnboardingController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\NotificationController;





    
// Auth Routes
Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Password reset
Route::get('password/reset', [AdminAuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [AdminAuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [AdminAuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [AdminAuthController::class, 'reset'])->name('password.update');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('account', [AdminProfileController::class, 'edit'])->name('account.edit');
    Route::post('account', [AdminProfileController::class, 'update'])->name('account.update');
    Route::post('/admin/account/avatar/delete', [AdminProfileController::class, 'deleteAvatar'])->name('admin.account.avatar.delete');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Dashboard
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/customers', [UserController::class, 'customers'])->name('customers');
        Route::get('/drivers', [UserController::class, 'drivers'])->name('drivers');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Driver Verification
    Route::prefix('verifications')->name('verifications.')->group(function () {
        Route::get('/', [DriverVerificationController::class, 'index'])->name('index');
        Route::get('/{driverProfile}', [DriverVerificationController::class, 'show'])->name('show');
        Route::post('/{driverProfile}/approve', [DriverVerificationController::class, 'approve'])->name('approve');
        Route::post('/{driverProfile}/reject', [DriverVerificationController::class, 'reject'])->name('reject');
        Route::post('/{driverProfile}/in-progress', [DriverVerificationController::class, 'markInProgress'])->name('in-progress');
    });
    
    // Services
    Route::resource('services', ServiceController::class);
    Route::post('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    
    // Jobs
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobController::class, 'index'])->name('index');
        Route::get('/{job}', [JobController::class, 'show'])->name('show');
        Route::post('/{job}/update-status', [JobController::class, 'updateStatus'])->name('update-status');
    });
    
    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
    });

    // 🔔 NOTIFICATION ROUTES - ADD THIS SECTION
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
        Route::post('/send', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('send');
        Route::post('/send-bulk', [\App\Http\Controllers\Admin\NotificationController::class, 'sendBulk'])->name('send-bulk');
        Route::post('/send-promotion', [DashboardController::class, 'sendPromotion'])->name('send-promotion');
        Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
        // 🔔 Admin Bell Notification (AJAX)
        Route::get('/bell', [NotificationController::class, 'bell'])
            ->name('bell');

        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->name('read');

    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
        Route::post('/settings/app-name', [SettingController::class, 'updateAppName'])->name('settings.app_name.update');

    });


    //  Route::get('/driver/upload-documents', [DriverOnboardingController::class, 'showUploadForm'])
    //     ->name('driver.documents.upload');
    
    // Route::post('/driver/upload-documents', [DriverOnboardingController::class, 'uploadDocuments'])
    //     ->name('driver.documents.upload');
    
    // Step 2: Service Selection
    // Route::get('/driver/select-services', [DriverOnboardingController::class, 'showServicesForm'])
    //     ->name('driver.services.select');
    
    // Route::post('/driver/save-services', [DriverOnboardingController::class, 'saveServices'])
    //     ->name('driver.services.save');
    
    // Step 3: Pricing Setup
    // Route::get('/driver/set-pricing', [DriverOnboardingController::class, 'showPricingForm'])
    //     ->name('driver.pricing.setup');
    
    // Route::post('/driver/save-pricing', [DriverOnboardingController::class, 'savePricing'])
    //     ->name('driver.pricing.save');
    
    // Step 4: Availability Setup
    // Route::get('/driver/set-availability', [DriverOnboardingController::class, 'showAvailabilityForm'])
    //     ->name('driver.availability.setup');
    
    // Route::post('/driver/save-availability', [DriverOnboardingController::class, 'saveAvailability'])
    //     ->name('driver.availability.save');
    
    // // Verification Status
    // Route::get('/driver/verification-status', [DriverOnboardingController::class, 'showVerificationStatus'])
    //     ->name('driver.verification.status');
});


