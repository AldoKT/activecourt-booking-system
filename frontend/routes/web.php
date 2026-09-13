<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;

// --- Guest & Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'user.about')->name('about');
Route::view('/contact', 'user.contact')->name('contact');
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    return back()->with('contact_success', true);
})->name('contact.submit');

// --- Auth Routes ---
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::post('/login', [AuthController::class, 'loginSubmit'])->name('login.submit');
Route::post('/register', [AuthController::class, 'registerSubmit'])->name('register.submit');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::get('/auth/google', [AuthController::class, 'googleAuth'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallbackForward'])->name('auth.google.callback');
Route::get('/auth/google/success', [AuthController::class, 'googleSuccess'])->name('auth.google.success');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Field Explorations ---
Route::get('/fields', [FieldController::class, 'index'])->name('fields.index');
Route::get('/fields/{field}', [FieldController::class, 'show'])->name('fields.show');

// --- Booking Actions & User Dashboard ---
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::post('/booking/{id}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');
Route::get('/booking/{id}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::get('/user/history', [BookingController::class, 'history'])->name('user.history');
Route::get('/booking/{id}', [BookingController::class, 'show'])
    ->name('booking.show');
//Tambahan bisa dihapus nanti kalo sudah deploy
Route::post('/payment/force-paid/{id}', [PaymentController::class, 'forcePaid'])
    ->name('payment.forcePaid');
Route::post('/payment/verify/{id}', [PaymentController::class, 'verify'])
    ->name('payment.verify');

// --- Invoice ---
Route::get('/booking/{id}/invoice', [BookingController::class, 'invoice'])->name('booking.invoice');

// --- Admin Portal ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Fields
    Route::get('/fields', [AdminController::class, 'fields'])->name('fields');
    Route::post('/fields', [AdminController::class, 'storeField'])->name('fields.store');
    Route::get('/fields/{id}/edit', [AdminController::class, 'editField'])->name('fields.edit');
    Route::put('/fields/{id}', [AdminController::class, 'updateField'])->name('fields.update');
    Route::delete('/fields/{id}', [AdminController::class, 'destroyField'])->name('fields.destroy');
    
    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{id}/ban', [AdminController::class, 'toggleBanUser'])->name('users.ban');
    
    // Bookings
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{id}', [AdminController::class, 'bookingDetail'])->name('bookings.detail');
    Route::get('/bookings/{id}/invoice', [AdminController::class, 'bookingInvoice'])->name('bookings.invoice');
    Route::post('/bookings/{id}/force-paid', [AdminController::class, 'forcePaid'])->name('bookings.forcePaid');
    Route::post('/bookings/{id}/cancel', [AdminController::class, 'cancelBooking'])->name('bookings.cancel');
    
    // Schedules
    Route::get('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules');
    Route::post('/schedules', [App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{id}', [App\Http\Controllers\Admin\ScheduleController::class, 'update'])->name('schedules.update');
    Route::post('/schedules/destroy-all', [App\Http\Controllers\Admin\ScheduleController::class, 'destroyAll'])->name('schedules.destroyAll');
    Route::delete('/schedules/{id}', [App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::post('/schedules/{id}/toggle', [App\Http\Controllers\Admin\ScheduleController::class, 'toggleStatus'])->name('schedules.toggle');
});

Route::post('/booking/cancel-reschedule', function() {
    session()->forget('reschedule_booking_id');
    return redirect()->route('user.history')->with('info', 'Reschedule dibatalkan.');
})->name('booking.cancel-reschedule');
