<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\OTPController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


// Route::middleware(['auth:sanctum','verified'])->group(function () {
    
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/send-register-otp',[OTPController::class,'sendRegisterOTP'])->middleware(['throttle:1,3']);
    Route::post('/verify-register-otp', [OTPController::class,'verifyRegisterOTP'])->middleware(['signed','throttle:3,1'])->name('verification.otp');
    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
});

Route::post('/send-reset-otp',[OTPController::class,'sendResetOTP'])->middleware('throttle:1,3');
Route::post('/verify-reset-otp',[OTPController::class,'verifyResetOTP'])->middleware('throttle:2,1');
Route::post('/reset-password',[OTPController::class,'resetPassword']);