<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsIndexController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\Draft\CreateCampaignController;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\Api\Controllers\UserController;

Route::get('user', UserController::class);

Route::apiResource('templates', TemplatesController::class);

Route::prefix('campaigns')->group(function () {
    Route::get('/', CampaignsIndexController::class);
    Route::post('/', CreateCampaignController::class);
});
