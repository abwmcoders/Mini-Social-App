<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::view('/', 'posts.index')->name('home');

Route::redirect('/', 'posts');

Route::middleware('guest')->group(function (){
    Route::get('/register', function () {
        return view('auth.register');
    })->middleware('guest')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::view('/forget-password', 'auth.forget-password')->name('password.request');
    Route::post('/forget-password', [ResetPasswordController::class, 'passwordEmail']);
    Route::view('/reset-password', [ResetPasswordController::class, 'passwordReset']);
    Route::post('/reset-password', [ResetPasswordController::class, 'passwordUpdate'])->name('password.update');
});

Route::middleware('auth')->group(function (){
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->middleware('verified')->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //! Email verification notice route
    Route::get('/email/verify', [AuthController::class, 'verifyNotice']
    )->middleware('auth')->name('verification.notice');

    //! Email verification handler
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed'])->name('verification.verify');

    //! Resending Email verification notice
    Route::post('/email/verification-notication', [AuthController::class, 'verifyHandler'])->middleware(['throttle:6,1'])->name('verification.send');
});

Route::resource('posts', PostController::class);

Route::get('/{user}/posts', [DashboardController::class, 'userPosts'])->name('posts.user');



