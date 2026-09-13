<?php

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'ActiveCourt API Backend is running.',
        'version' => '1.0.0',
        'environment' => app()->environment()
    ]);
});

Route::get('/auth/google', [AuthController::class, 'googleAuth'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');