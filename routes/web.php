<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\ProfilePasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\StravaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

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

// Activities
Route::get('/activities', [ActivityController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('activities.index');

Route::get('/activities/{activity}', [ActivityController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('activities.show');

// Strava OAuth
Route::get('/strava/redirect', [StravaController::class, 'redirect'])
    ->middleware(['auth', 'verified'])
    ->name('strava.redirect');

Route::get('/strava/callback', [StravaController::class, 'callback'])
    ->middleware(['auth', 'verified'])
    ->name('strava.callback');

Route::delete('/strava/disconnect', [StravaController::class, 'disconnect'])
    ->middleware(['auth', 'verified'])
    ->name('strava.disconnect');

Route::post('/strava/sync-historical', [StravaController::class, 'syncHistorical'])
    ->middleware(['auth', 'verified'])
    ->name('strava.sync-historical');

// Premium landing page (accessible to all authenticated users)
Route::get('/premium', [PremiumController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('premium.index');

// Premium features (EnsurePremium middleware)
Route::middleware(['auth', 'verified', 'premium'])->prefix('premium')->name('premium.')->group(function () {
    Route::get('/trends', fn () => view('premium.trends'))->name('trends');
    Route::get('/compare', fn () => view('premium.compare'))->name('compare');
    Route::get('/year-over-year', fn () => view('premium.year-over-year'))->name('year-over-year');
});

// Impersonation
Route::post('/impersonate/start/{user}', [ImpersonateController::class, 'start'])
    ->middleware('auth')
    ->name('impersonate.start');

Route::post('/impersonate/stop', [ImpersonateController::class, 'stop'])
    ->middleware('auth')
    ->name('impersonate.stop');
