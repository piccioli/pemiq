<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\ProfilePasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StravaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

// Registration
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// Email verification
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:1,1'])
    ->name('verification.send');

// Password reset
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// Profile (authenticated)
Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('profile.show');

Route::put('/profile', [ProfileController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('profile.update');

// Profile password change (authenticated)
Route::get('/profile/password', [ProfilePasswordController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('profile.password');

Route::put('/profile/password', [ProfilePasswordController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('profile.password.update');

// Logout
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Strava OAuth
Route::get('/strava/redirect', [StravaController::class, 'redirect'])
    ->middleware(['auth', 'verified'])
    ->name('strava.redirect');

Route::get('/strava/callback', [StravaController::class, 'callback'])
    ->middleware(['auth', 'verified'])
    ->name('strava.callback');
