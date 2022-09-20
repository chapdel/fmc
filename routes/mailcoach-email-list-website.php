<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListWebsiteController;

Route::get('{emailListWebsiteSlug}', [EmailListWebsiteController::class, 'index'])->name('website');
Route::get('{emailListWebsiteSlug}/{campaignUuid}', [EmailListWebsiteController::class, 'show'])->name('website.campaign');
