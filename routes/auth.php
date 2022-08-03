<?php

use Spatie\Mailcoach\Http\Auth\Controllers\ForgotPasswordController;
use Spatie\Mailcoach\Http\Auth\Controllers\LoginController;
use Spatie\Mailcoach\Http\Auth\Controllers\ResetPasswordController;
use Spatie\Mailcoach\Http\Auth\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Spatie\WelcomeNotification\WelcomesNewUsers;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('forgot-password');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::group(['middleware' => ['web', WelcomesNewUsers::class]], function () {
    Route::get('welcome/{user}', [WelcomeController::class, 'showWelcomeForm'])->name('welcome');
    Route::post('welcome/{user}', [WelcomeController::class, 'savePassword']);
});
