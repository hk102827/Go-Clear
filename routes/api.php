<?php

// routes/api.php - FOR FLUTTER APP

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\JobController;

/*
|--------------------------------------------------------------------------
| API Routes - FOR FLUTTER APP
|--------------------------------------------------------------------------
*/

// Public Routes (No Authentication Required)
Route::prefix('v1')->group(function () {
    
    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    // Get all services (for registration)
    Route::get('/services', [ServiceController::class, 'index']);
});

// Protected Routes (Require Authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Common Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // ========== DRIVER ROUTES ==========
    Route::prefix('driver')->middleware('auth:sanctum')->group(function () {
        
        // Document Upload
        Route::post('/documents/upload', [DriverController::class, 'uploadDocuments']);
        Route::get('/documents/status', [DriverController::class, 'getDocumentStatus']);
        Route::post('/documents/reupload', [DriverController::class, 'reuploadDocuments']);
        
        // Service Selection
        Route::post('/services/select', [DriverController::class, 'selectServices']);
        Route::get('/services/my-services', [DriverController::class, 'getMyServices']);
        
        // Pricing Setup

        Route::post('/services/save', [DriverController::class, 'saveServices']);
        Route::post('/pricing/save', [DriverController::class, 'savePricing']);
        Route::post('/availability/save', [DriverController::class, 'saveAvailability']);

        // Route::post('/pricing/save', [DriverController::class, 'savePricing']);
        // Route::get('/pricing', [DriverController::class, 'getPricing']);
        // Route::put('/pricing/update', [DriverController::class, 'updatePricing']);
        
        // Availability
        // Route::post('/availability/save', [DriverController::class, 'saveAvailability']);
        // Route::get('/availability', [DriverController::class, 'getAvailability']);
        // Route::put('/availability/update', [DriverController::class, 'updateAvailability']);
        
        // Online/Offline Status
        Route::post('/status/toggle', [DriverController::class, 'toggleOnlineStatus']);
        
        // Dashboard
        Route::get('/dashboard', [DriverController::class, 'dashboard']);
        Route::get('/earnings', [DriverController::class, 'earnings']);
        Route::get('/jobs', [DriverController::class, 'getJobs']);
        Route::get('/jobs/{id}', [DriverController::class, 'getJobDetail']);
        
        // Job Actions
        Route::post('/jobs/{id}/accept', [DriverController::class, 'acceptJob']);
        Route::post('/jobs/{id}/reject', [DriverController::class, 'rejectJob']);
        Route::post('/jobs/{id}/complete', [DriverController::class, 'completeJob']);
        Route::post('/jobs/{id}/cancel', [DriverController::class, 'cancelJob']);
    });
    
    // ========== CUSTOMER ROUTES ==========
    Route::prefix('customer')->middleware('role:customer')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [CustomerController::class, 'dashboard']);
        
        // Browse Drivers
        Route::get('/drivers', [CustomerController::class, 'getDrivers']);
        Route::get('/drivers/{id}', [CustomerController::class, 'getDriverDetail']);
        Route::get('/drivers/search', [CustomerController::class, 'searchDrivers']);
        
        // Booking
        Route::post('/bookings/create', [CustomerController::class, 'createBooking']);
        Route::get('/bookings', [CustomerController::class, 'getBookings']);
        Route::get('/bookings/{id}', [CustomerController::class, 'getBookingDetail']);
        Route::post('/bookings/{id}/cancel', [CustomerController::class, 'cancelBooking']);
        
        // Reviews
        Route::post('/bookings/{id}/review', [CustomerController::class, 'submitReview']);
    });
});


// use Illuminate\Support\Facades\Route;
// use App\Http\
// .Controllers\API\AuthController;
// use App\Http\Controllers\API\CustomerController;
// use App\Http\Controllers\API\DriverController;
// use App\Http\Controllers\API\JobController;
// use App\Http\Controllers\API\ServiceController;

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/services', [ServiceController::class, 'index']);

// Route::middleware('auth:sanctum')->group(function () {
 
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/profile', [AuthController::class, 'profile']);
//     Route::put('/profile', [AuthController::class, 'updateProfile']);
 
    Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
        Route::post('/jobs', [JobController::class, 'create']);
        Route::get('/jobs', [JobController::class, 'customerJobs']);
        Route::get('/jobs/{job}', [JobController::class, 'show']);
        Route::post('/jobs/{job}/cancel', [JobController::class, 'cancel']);
        Route::post('/jobs/{job}/rate', [JobController::class, 'rate']);
    });

//     Route::prefix('driver')->middleware('role:driver')->group(function () {
//         Route::get('/dashboard', [DriverController::class, 'dashboard']);
//         Route::post('/verification', [DriverController::class, 'submitVerification']);
//         Route::get('/verification-status', [DriverController::class, 'verificationStatus']);
//         Route::post('/services', [DriverController::class, 'addServices']);
//         Route::get('/services', [DriverController::class, 'myServices']);
//         Route::post('/toggle-online', [DriverController::class, 'toggleOnline']);
//         Route::get('/jobs', [JobController::class, 'driverJobs']);
//         Route::get('/jobs/available', [DriverController::class, 'availableJobs']);
//         Route::post('/jobs/{job}/accept', [JobController::class, 'acceptJob']);
//         Route::post('/jobs/{job}/start', [JobController::class, 'startJob']);
//         Route::post('/jobs/{job}/complete', [JobController::class, 'completeJob']);
//         Route::get('/earnings', [DriverController::class, 'earnings']);
//         Route::post('/location', [DriverController::class, 'updateLocation']);
//     });
// });