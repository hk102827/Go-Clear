<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverRequestController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\SavedAddressController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AdminController;



// Public Routes (No Authentication Required)
Route::prefix('v1')->group(function () {

    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/services', [ServiceController::class, 'index']);
     Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        // Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/profile', [AuthController::class, 'getProfile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        //  Route::get('/user', [AuthController::class, 'user']);
        Route::get('/profile', [CustomerProfileController::class, 'getProfile']);
        Route::put('/profile', [CustomerProfileController::class, 'updateProfile']);
        Route::post('/change-password', [CustomerProfileController::class, 'changePassword']);

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
            Route::get('/status', [DriverController::class, 'getOnlineStatus']);
            Route::get('/dashboard', [DriverController::class, 'dashboard']);
            Route::get('/jobs', [DriverController::class, 'driverJobs']);
            Route::post('/bank-details', [DriverController::class, 'addBankDetails']);
            Route::get('/bank-details', [DriverController::class, 'getBankDetails']);
            Route::put('/bank-details', [DriverController::class, 'updateBankDetails']);
            Route::get('/wallet', [DriverController::class, 'wallet']);
            Route::get('/earnings-summary', [DriverController::class, 'earningsSummary']); // ← ADD THIS
            Route::post('/withdraw-request', [DriverController::class, 'withdrawRequest']);
            Route::get('/withdrawal-history', [DriverController::class, 'withdrawalHistory']);
            Route::get('/pending-withdrawal', [DriverController::class, 'getPendingWithdrawal']);
            Route::get('/transactions', [DriverController::class, 'transactions']);
            // Location Updates
            Route::post('/location/update', [DriverController::class, 'updateLocation']);
            Route::get('/location', [DriverController::class, 'getLocation']); // optional
             // ✅ Driver incoming requests (NEW)
            Route::get('/requests', [DriverRequestController::class, 'index']);
            Route::post('/requests/{jobRequest}/accept', [DriverRequestController::class, 'accept']);
            Route::post('/requests/{jobRequest}/reject', [DriverRequestController::class, 'reject']);
            Route::post('/jobs/{job}/start', [JobController::class, 'startJob']);
            // Route::post('/jobs/{job}/complete', [JobController::class, 'completeJob']);
            Route::post('/jobs/{id}/reject', [DriverController::class, 'rejectJob']);
            Route::post('/jobs/{job}/complete', [JobController::class, 'completeJob']);
            Route::post('/jobs/{id}/cancel', [DriverController::class, 'cancelJob']);
            Route::delete('/account/delete', [DriverController::class, 'deleteAccount']);
            Route::delete('/bankaccount/delete', [DriverController::class, 'deleteBankAccount']);

        });
        Route::post('/jobs/{job}/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::get('/payment-intent/{paymentIntentId}/status', [PaymentController::class, 'checkPaymentStatus']);
        Route::post('/payment-intent/{paymentIntentId}/cancel', [PaymentController::class, 'cancelPayment']);
        Route::prefix('admin')->group(function () {
        Route::get('/pending-bank-details', [AdminController::class, 'getPendingBankDetails']);
        Route::get('/bank-details', [AdminController::class, 'getAllBankDetails']);
        Route::post('/bank-details/{id}/approve', [AdminController::class, 'approveBankDetails']);

        
        // Withdrawal Management
        Route::get('/pending-withdrawals', [AdminController::class, 'getPendingWithdrawals']);
        Route::get('/withdrawals', [AdminController::class, 'getAllWithdrawals']);
        Route::post('/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal']);
        Route::get('/withdrawal-stats', [AdminController::class, 'getWithdrawalStats']);
    });

        Route::prefix('customer')->group(function () {
            Route::get('/dashboard', [CustomerController::class, 'dashboard']);
            Route::post('/destination/set', [CustomerController::class, 'setDestination']);
            Route::get('/drivers/nearby', [CustomerController::class, 'nearbyDrivers']);
            Route::get('/bookings/{job}/track', [CustomerController::class, 'trackDriver']);
            Route::get('/bookings/{job}/receipt', [CustomerController::class, 'receipt']);
            Route::get('/transactions', [DriverController::class, 'transactions']);


            // Bookings (JobController)
            Route::post('/bookings', [JobController::class, 'create']);
            Route::get('/bookings', [JobController::class, 'customerJobs']);
            Route::get('/bookings/{job}', [JobController::class, 'show']);
            // Route::post('/bookings/{job}/cancel', [JobController::class, 'cancel']);
            Route::post('/bookings/{job}/rate', [JobController::class, 'rate']);
                    // ✅ Customer Confirm (NEW)
            Route::post('/bookings/{job}/confirm', [JobController::class, 'confirmBooking']);
            Route::post('/bookings/{job}/cancel', [JobController::class, 'cancel']);
            Route::post('/bookings/{job}/rate', [JobController::class, 'rate']);
            Route::get('/addresses', [SavedAddressController::class, 'index']);
            Route::post('/addresses', [SavedAddressController::class, 'store']);
            Route::put('/addresses/{id}', [SavedAddressController::class, 'update']);
            Route::delete('/addresses/{id}', [SavedAddressController::class, 'destroy']);
            Route::get('/history', [CustomerController::class, 'customerHistory']);

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
