<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Front\Controllers\CampaignWebviewController;
use Spatie\Mailcoach\Http\Front\Controllers\ConfirmSubscriberController;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListCampaignsFeedController;
use Spatie\Mailcoach\Http\Front\Controllers\SubscribeController;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;

Route::get('/confirm-subscription/{subscriberUuid}', '\\' . ConfirmSubscriberController::class)->name('mailcoach.confirm');

Route::get('/unsubscribe/{subscriberUuid}/{sendUuid?}',  ['\\' .UnsubscribeController::class, 'show'])->name('mailcoach.unsubscribe');

Route::post('/unsubscribe/{subscriberUuid}/{sendUuid?}',  ['\\' .UnsubscribeController::class, 'confirm']);

Route::get('webview/{campaignUuid}', '\\' . CampaignWebviewController::class)->name('mailcoach.webview');

Route::get('feed/{emailListUuid}', '\\' . EmailListCampaignsFeedController::class)->name('mailcoach.feed');

Route::post('subscribe/{emailListUuid}', '\\' . SubscribeController::class)->name('mailcoach.subscribe');
