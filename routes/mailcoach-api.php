<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendTestEmailController;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\Api\Controllers\UserController;

Route::get('user', UserController::class);

Route::apiResource('templates', TemplatesController::class);
Route::apiResource('campaigns', CampaignsController::class);

Route::prefix('campaigns/{campaign}')->group(function () {
    Route::post('send-test', SendTestEmailController::class);
});
