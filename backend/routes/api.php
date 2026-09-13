<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;

// Public API routes
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login-google', [AuthController::class, 'apiLoginGoogle']);
Route::get('/fields', [FieldController::class, 'apiIndex']);
Route::get('/fields/{field}', [FieldController::class, 'apiShow']);
Route::post('/midtrans/callback', [PaymentController::class, 'callback']);

// Authenticated API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/booking', [BookingController::class, 'apiStore']);
    Route::post('/booking/{id}/cancel', [BookingController::class, 'apiCancel']);
    Route::post('/booking/{id}/reschedule', [BookingController::class, 'apiReschedule']);
    Route::get('/booking/{id}/checkout', [BookingController::class, 'apiCheckout']);
    Route::get('/user/history', [BookingController::class, 'apiHistory']);
    Route::get('/booking/{id}', [BookingController::class, 'apiShowDetail']);
    Route::get('/booking/{id}/invoice', [BookingController::class, 'apiInvoice']);
    Route::post('/payment/force-paid/{id}', [PaymentController::class, 'forcePaid']);
    Route::post('/payment/verify/{id}', [PaymentController::class, 'verify']);

    // Admin API routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'apiDashboard']);
        
        // Fields
        Route::get('/fields', [AdminController::class, 'apiFields']);
        Route::post('/fields', [AdminController::class, 'apiStoreField']);
        Route::get('/fields/{id}/edit', [AdminController::class, 'apiEditField']);
        Route::post('/fields/{id}', [AdminController::class, 'apiUpdateField']); // using POST to support file uploads with multipart
        Route::delete('/fields/{id}', [AdminController::class, 'apiDestroyField']);
        
        // Users
        Route::get('/users', [AdminController::class, 'apiUsers']);
        Route::get('/users/{id}/edit', [AdminController::class, 'apiEditUser']);
        Route::put('/users/{id}', [AdminController::class, 'apiUpdateUser']);
        Route::post('/users/{id}/ban', [AdminController::class, 'apiToggleBanUser']);
        
        // Bookings
        Route::get('/bookings', [AdminController::class, 'apiBookings']);
        Route::get('/bookings/{id}', [AdminController::class, 'apiBookingDetail']);
        Route::get('/bookings/{id}/invoice', [AdminController::class, 'apiBookingInvoice']);
        Route::post('/bookings/{id}/force-paid', [AdminController::class, 'apiForcePaid']);
        Route::post('/bookings/{id}/cancel', [AdminController::class, 'apiCancelBooking']);
        
        // Schedules
        Route::get('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'apiIndex']);
        Route::post('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'apiStore']);
        Route::put('/schedules/{id}', [App\Http\Controllers\Admin\ScheduleController::class, 'apiUpdate']);
        Route::post('/schedules/destroy-all', [App\Http\Controllers\Admin\ScheduleController::class, 'apiDestroyAll']);
        Route::delete('/schedules/{id}', [App\Http\Controllers\Admin\ScheduleController::class, 'apiDestroy']);
        Route::post('/schedules/{id}/toggle', [App\Http\Controllers\Admin\ScheduleController::class, 'apiToggleStatus']);
    });
});
