<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignClicksController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignOpensController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignUnsubscribesController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendCampaignController;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendTestEmailController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\EmailListsController;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\Api\Controllers\UserController;

Route::get('user', UserController::class);

Route::apiResource('templates', TemplatesController::class);

Route::apiResource('campaigns', CampaignsController::class);
Route::prefix('campaigns/{campaign}')->group(function () {
    Route::post('send-test', SendTestEmailController::class);
    Route::post('send', SendCampaignController::class);

    Route::get('opens', CampaignOpensController::class);
    Route::get('clicks', CampaignClicksController::class);
    Route::get('unsubscribes', CampaignUnsubscribesController::class);
});

Route::apiResource('email-lists', EmailListsController::class);

Route::apiResource('email-lists.subscribers', SubscribersController::class);
