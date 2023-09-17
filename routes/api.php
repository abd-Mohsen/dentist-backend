<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/send-register-otp',[OTPController::class,'sendRegisterOTP'])->middleware(['throttle:3,1']);
    Route::post('/verify-register-otp', [OTPController::class,'verifyRegisterOTP'])->middleware(['signed','throttle:3,1'])->name('verification.otp');
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/upload-profile-image', [UserController::class, 'uploadProfileImage']);
});

Route::post('/send-reset-otp',[OTPController::class,'sendResetOTP'])->middleware('throttle:3,1');
Route::post('/verify-reset-otp',[OTPController::class,'verifyResetOTP'])->middleware('throttle:3,1');
Route::post('/reset-password',[OTPController::class,'resetPassword']);

Route::middleware(['auth:sanctum','verified'])->group(function () {
    Route::post('/edit-profile', [UserController::class, 'editProfile']);
    Route::post('/edit-password', [UserController::class, 'editPassword']);
});

Route::apiResources([
    'products'=> ProductController::class,
    'categories'=> ProductController::class,
]);