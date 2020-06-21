<?php

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\CampaignsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DestroyCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CreateCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\ScheduleCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendTestEmailController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\UnscheduleCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\RetryFailedSendsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignClicksController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignOpensController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignSummaryController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\CampaignUnsubscribesController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Sent\OutboxController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\DestroyEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\EmailListSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\EmailListsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\CreateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DestroySegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DuplicateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\EditSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\SegmentsIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\SegmentSubscribersIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\CreateSubscriberController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroyAllUnsubscribedController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroySubscriberController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\ReceivedCampaignsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\ResendConfirmationMailController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscriberDetailsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscribersExportController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscribersIndexController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus\ConfirmController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus\ResubscribeController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus\UnsubscribeController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\TagsController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DestroySubscriberImportController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DownloadSubscriberImportAttachmentController;
use Spatie\Mailcoach\Http\App\Controllers\TemplatesController;
use Spatie\Mailcoach\Http\App\Middleware\EditableCampaign;

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\' . CampaignsIndexController::class)->name('mailcoach.campaigns');
    Route::post('/', '\\' . CreateCampaignController::class)->name('mailcoach.campaigns.store');

    Route::prefix('{campaign}')->group(function () {
        Route::middleware('\\' . EditableCampaign::class)->group(function () {
            Route::get('settings', ['\\' . CampaignSettingsController::class, 'edit'])->name('mailcoach.campaigns.settings');
            Route::put('settings', ['\\' . CampaignSettingsController::class, 'update']);

            Route::get('content', ['\\' . CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
            Route::put('content', ['\\' . CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');

            Route::get('delivery', '\\' . CampaignDeliveryController::class)->name('mailcoach.campaigns.delivery');

            Route::post('send-test-email', '\\' . SendTestEmailController::class)->name('mailcoach.campaigns.sendTestEmail');
            Route::post('schedule', '\\' . ScheduleCampaignController::class)->name('mailcoach.campaigns.schedule');
            Route::post('unschedule', '\\' . UnscheduleCampaignController::class)->name('mailcoach.campaigns.unschedule');
            Route::post('send', '\\' . SendCampaignController::class)->name('mailcoach.campaigns.send');
        });

        Route::get('summary', '\\' . CampaignSummaryController::class)->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\' . CampaignOpensController::class)->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\' . CampaignClicksController::class)->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\' . CampaignUnsubscribesController::class)->name('mailcoach.campaigns.unsubscribes');

        Route::get('outbox', '\\' . OutboxController::class)->name('mailcoach.campaigns.outbox');

        Route::delete('/', '\\' . DestroyCampaignController::class)->name('mailcoach.campaigns.delete');
        Route::post('duplicate', '\\' . DuplicateCampaignController::class)->name('mailcoach.campaigns.duplicate');
        Route::post('retry-failed-sends', '\\' . RetryFailedSendsController::class)->name('mailcoach.campaigns.retry-failed-sends');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\' . EmailListsIndexController::class)->name('mailcoach.emailLists');
    Route::post('/', '\\' . CreateEmailListController::class)->name('mailcoach.emailLists.store');

    Route::prefix('{emailList}')->group(function () {
        Route::get('subscribers', '\\' . SubscribersIndexController::class)->name('mailcoach.emailLists.subscribers');
        Route::post('subscribers/export', '\\' . SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');

        Route::post('subscriber/create', ['\\' . CreateSubscriberController::class, 'store'])->name('mailcoach.emailLists.subscriber.store');
        Route::prefix('subscriber/{subscriber}')->group(function () {
            Route::get('details', ['\\' . SubscriberDetailsController::class, 'edit'])->name('mailcoach.emailLists.subscriber.details');
            Route::put('details', ['\\' . SubscriberDetailsController::class, 'update']);
            Route::get('received-campaigns', '\\' . ReceivedCampaignsController::class)->name('mailcoach.emailLists.subscriber.receivedCampaigns');
            Route::delete('/', '\\' . DestroySubscriberController::class)->name('mailcoach.emailLists.subscriber.delete');
        });

        Route::delete('unsubscribes', '\\' . DestroyAllUnsubscribedController::class)->name('mailcoach.emailLists.destroy-unsubscribes');

        Route::get('import-subscribers', ['\\' . ImportSubscribersController::class, 'showImportScreen'])->name('mailcoach.emailLists.import-subscribers');
        Route::post('import-subscribers', ['\\' . ImportSubscribersController::class, 'import']);

        Route::get('settings', ['\\' . EmailListSettingsController::class, 'edit'])->name('mailcoach.emailLists.settings');
        Route::put('settings', ['\\' . EmailListSettingsController::class, 'update']);

        Route::prefix('tags')->group(function () {
            Route::get('/', ['\\' . TagsController::class, 'index'])->name('mailcoach.emailLists.tags');
            Route::post('/', ['\\' . TagsController::class, 'store'])->name('mailcoach.emailLists.tag.store');
            Route::prefix('{tag}')->group(function () {
                Route::get('/', ['\\' . TagsController::class, 'edit'])->name('mailcoach.emailLists.tag.edit');
                Route::put('/', ['\\' . TagsController::class, 'update']);
                Route::delete('/', ['\\' . TagsController::class, 'destroy'])->name('mailcoach.emailLists.tag.delete');
            });
        });
        Route::delete('/', '\\' . DestroyEmailListController::class)->name('mailcoach.emailLists.delete');

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\' . SegmentsIndexController::class)->name('mailcoach.emailLists.segments');

            Route::post('/', '\\' . CreateSegmentController::class)->name('mailcoach.emailLists.segment.store');

            Route::prefix('{segment}')->group(function () {
                Route::get('subscribers', '\\' . SegmentSubscribersIndexController::class)->name('mailcoach.emailLists.segment.subscribers');
                Route::get('/details', ['\\' . EditSegmentController::class, 'edit'])->name('mailcoach.emailLists.segment.edit');
                Route::put('/details', ['\\' . EditSegmentController::class, 'update']);
                Route::delete('/', '\\' . DestroySegmentController::class)->name('mailcoach.emailLists.segment.delete');
                Route::post('duplicate', '\\' .  DuplicateSegmentController::class)->name('mailcoach.emailLists.segment.duplicate');
            });
        });
    });
});

Route::prefix('subscriber-import')->group(function () {
    Route::get('{subscriberImport}/download-attachment/{collection}', '\\' . DownloadSubscriberImportAttachmentController::class)->name('mailcoach.subscriberImport.downloadAttachment');
    Route::delete('{subscriberImport}', '\\' . DestroySubscriberImportController::class)->name('mailcoach.subscriberImport.delete');
});

Route::prefix('subscriber/{subscriber}')->group(function () {
    Route::post('resend-confirmation-mail', '\\' . ResendConfirmationMailController::class)->name('mailcoach.subscriber.resend-confirmation-mail');
    Route::post('confirm', '\\' . ConfirmController::class)->name('mailcoach.subscriber.confirm');
    Route::post('unsubscribe', '\\' . UnsubscribeController::class)->name('mailcoach.subscriber.unsubscribe');
    Route::post('subscribe', '\\' . ResubscribeController::class)->name('mailcoach.subscriber.resubscribe');
});

Route::prefix('templates')->group(function () {
    Route::get('/', ['\\' . TemplatesController::class, 'index'])->name('mailcoach.templates');
    Route::post('/', ['\\' . TemplatesController::class, 'store'])->name('mailcoach.templates.store');
    Route::prefix('{template}')->group(function () {
        Route::get('/', ['\\' . TemplatesController::class, 'edit'])->name('mailcoach.templates.edit');
        Route::put('/', ['\\' . TemplatesController::class, 'update']);
        Route::delete('/', ['\\' . TemplatesController::class, 'destroy'])->name('mailcoach.templates.delete');
        Route::post('duplicate', ['\\' . TemplatesController::class, 'duplicate'])->name('mailcoach.templates.duplicate');
    });
});

Route::prefix('landing')->group(function () {
    Route::view('/subscribed', 'mailcoach::landingPages.subscribed')->name('mailcoach.landingPages.example');
});
