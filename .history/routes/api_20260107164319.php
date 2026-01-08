<?php

// routes/api.php - FOR FLUTTER APP

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;


// Public Routes (No Authentication Required)
Route::prefix('v1')->group(function () {

    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/services', [ServiceController::class, 'index']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        // Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        // ========== DRIVER ROUTES ==========
        Route::prefix('driver')->group(function () {

            // Document Upload
            Route::post('/vehicle/details', [DriverController::class, 'uploadVehicleDetails']);
            Route::get('/vehicle/details', [DriverController::class, 'getVehicleDetails']);
            Route::post('/documents/upload', [DriverController::class, 'uploadDocuments']);
            Route::get('/documents/status', [DriverController::class, 'getDocumentStatus']);
            Route::post('/documents/reupload', [DriverController::class, 'reuploadDocuments']);

            Route::post('/services/select', [DriverController::class, 'selectServices']);
            Route::get('/services/my-services', [DriverController::class, 'getMyServices']);
            Route::post('/services/save', [DriverController::class, 'saveServices']);
            Route::put('/services', [DriverController::class, 'updateService']);
            // Route::put('/services', [DriverController::class, 'updateMultipleServices']);
            Route::get('/get/services', [DriverController::class, 'getServices']);
            Route::post('/pricing/save', [DriverController::class, 'savePricing']);
            Route::post('/availability/save', [DriverController::class, 'saveAvailability']);
            Route::get('/availability', [DriverController::class, 'getAvailability']);

            // Online/Offline Status
            Route::post('/status/toggle', [DriverController::class, 'toggleOnlineStatus']);
            Route::get('/dashboard', [DriverController::class, 'dashboard']);
            // Route::get('/earnings', [DriverController::class, 'earnings']);
            Route::get('/jobs', [DriverController::class, 'getJobs']);
            Route::get('/jobs/{id}', [DriverController::class, 'getJobDetail']);
            Route::get('/wallet', [DriverController::class, 'wallet']);
            Route::get('/transactions', [DriverController::class, 'transactions']);
            Route::post('/withdraw', [DriverController::class, 'withdrawRequest']);
            // Location Updates
            Route::post('/location/update', [DriverController::class, 'updateLocation']);
            Route::get('/location', [DriverController::class, 'getLocation']); // optional


            // Job Actions
            Route::post('/jobs/{job}/start', [JobController::class, 'startJob']);
            Route::post('/jobs/{job}/accept', [JobController::class, 'acceptJob']);
            Route::post('/jobs/{id}/reject', [DriverController::class, 'rejectJob']);
            Route::post('/jobs/{job}/complete', [JobController::class, 'completeJob']);
            Route::post('/jobs/{id}/cancel', [DriverController::class, 'cancelJob']);
        });


        Route::prefix('customer')->group(function () {
            Route::get('/dashboard', [CustomerController::class, 'dashboard']);
            Route::post('/destination/set', [CustomerController::class, 'setDestination']);
            Route::get('/drivers/nearby', [CustomerController::class, 'nearbyDrivers']);
            Route::get('/bookings/{job}/track', [CustomerController::class, 'trackDriver']);
            Route::get('/bookings/{job}/receipt', [CustomerController::class, 'receipt']);

            // Bookings (JobController)
            Route::post('/bookings', [JobController::class, 'create']);
            Route::get('/bookings', [JobController::class, 'customerJobs']);
            Route::get('/bookings/{job}', [JobController::class, 'show']);
            Route::post('/bookings/{job}/cancel', [JobController::class, 'cancel']);
            Route::post('/bookings/{job}/rate', [JobController::class, 'rate']);
                    // ✅ Customer Confirm (NEW)
        Route::post('/bookings/{job}/confirm', [JobController::class, 'confirmBooking']);

        Route::post('/bookings/{job}/cancel', [JobController::class, 'cancel']);
        Route::post('/bookings/{job}/rate', [JobController::class, 'rate']);

        });
        // Notifications Routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::get('/{notification}', [NotificationController::class, 'show']);
            Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead']);
            Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        });
    });

});
