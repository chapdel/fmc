<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\Api\Controllers\UserController;

Route::get('user', UserController::class);

Route::apiResource('templates', TemplatesController::class);

Route::prefix('campaigns')->group(function () {
    Route::apiResource('/', CampaignsController::class);
});
