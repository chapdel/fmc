<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationActionsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailSummaryController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\DestroyAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\DuplicateAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\SendAutomationMailTestController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\DestroyAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\DuplicateAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\RunAutomationController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignTestController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\DuplicateCampaignController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\RetryFailedSendsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\TemplatesController;
use Spatie\Mailcoach\Http\App\Controllers\DebugController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\DestroyEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\CreateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DuplicateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\EditSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListGeneralSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListMailersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListOnboardingController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroyAllUnsubscribedController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscriberDetailsController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscribersExportController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SummaryController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\TagsController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DestroySubscriberImportController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DownloadSubscriberImportAttachmentController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\ResendTransactionalMailController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\ShowTransactionalMailBodyController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\SendTransactionalMailTestController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\TransactionalMailSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\TransactionalMailTemplatesController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\TransactionalMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\TransactionalMailPerformanceController;
use Spatie\Mailcoach\Http\App\Middleware\EditableCampaign;

Route::get('debug', '\\' . DebugController::class)->name('debug');

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('campaigns', Config::getLivewireComponentClass('campaigns', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns::class)))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', Config::getLivewireComponentClass('campaign-settings', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettings::class))->name('mailcoach.campaigns.settings');
        Route::get('content', ['\\' . CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
        Route::get('delivery', Config::getLivewireComponentClass('campaign-delivery', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDelivery::class))->name('mailcoach.campaigns.delivery');

        Route::middleware('\\' . EditableCampaign::class)->group(function () {
            Route::put('content', ['\\' . CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');

            Route::post('send-test-email', '\\' . SendCampaignTestController::class)->name('mailcoach.campaigns.sendTestEmail');
        });

        Route::get('summary', '\\' . Config::getLivewireComponentClass('campaign-summary', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummary::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\' . Config::getLivewireComponentClass('campaign-opens', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpens::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\' . Config::getLivewireComponentClass('campaign-links', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicks::class))->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\' . Config::getLivewireComponentClass('campaign-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribes::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\' . Config::getLivewireComponentClass('campaign-outbox', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutbox::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('lists', \Spatie\Mailcoach\Http\App\Livewire\Audience\Lists::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\' . SummaryController::class)->name('mailcoach.emailLists.summary');
        Route::get('subscribers', '\\' . Config::getLivewireComponentClass('subscribers', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscribers::class))->name('mailcoach.emailLists.subscribers');
        Route::post('subscribers/export', '\\' . SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');

        Route::prefix('subscriber/{subscriber}')->group(function () {
            Route::get('details', ['\\' . SubscriberDetailsController::class, 'edit'])->name('mailcoach.emailLists.subscriber.details');
            Route::put('details', ['\\' . SubscriberDetailsController::class, 'update']);
            Route::get('attributes', ['\\' . SubscriberDetailsController::class, 'attributes'])->name('mailcoach.emailLists.subscriber.attributes');
            Route::get('sends', '\\' . Config::getLivewireComponentClass('subscriber-sends', \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberSends::class))->name('mailcoach.emailLists.subscriber.receivedCampaigns');
        });

        Route::delete('unsubscribes', '\\' . DestroyAllUnsubscribedController::class)->name('mailcoach.emailLists.destroy-unsubscribes');

        Route::get('subscribers/import-subscribers', ['\\' . ImportSubscribersController::class, 'showImportScreen'])->name('mailcoach.emailLists.import-subscribers');
        Route::post('subscribers/import-subscribers', ['\\' . ImportSubscribersController::class, 'import']);

        Route::get('general-settings', ['\\' . EmailListGeneralSettingsController::class, 'edit'])->name('mailcoach.emailLists.general-settings');
        Route::put('general-settings', ['\\' . EmailListGeneralSettingsController::class, 'update']);

        Route::get('onboarding', ['\\' . EmailListOnboardingController::class, 'edit'])->name('mailcoach.emailLists.onboarding');
        Route::put('onboarding', ['\\' . EmailListOnboardingController::class, 'update']);

        Route::get('mailers', ['\\' . EmailListMailersController::class, 'edit'])->name('mailcoach.emailLists.mailers');
        Route::put('mailers', ['\\' . EmailListMailersController::class, 'update']);

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\' . Config::getLivewireComponentClass('tags', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tags::class))->name('mailcoach.emailLists.tags');
            Route::post('/', ['\\' . TagsController::class, 'store'])->name('mailcoach.emailLists.tag.store');
            Route::prefix('{tag}')->group(function () {
                Route::get('/', ['\\' . TagsController::class, 'edit'])->name('mailcoach.emailLists.tag.edit');
                Route::put('/', ['\\' . TagsController::class, 'update']);
            });
        });
        Route::delete('/', '\\' . DestroyEmailListController::class)->name('mailcoach.emailLists.delete');

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\' . Config::getLivewireComponentClass('segments', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segments::class))->name('mailcoach.emailLists.segments');

            Route::post('/', '\\' . CreateSegmentController::class)->name('mailcoach.emailLists.segment.store');

            Route::prefix('{segment}')->group(function () {
                Route::get('subscribers', '\\' . Config::getLivewireComponentClass('segment-subscribers', \Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentSubscribers::class))->name('mailcoach.emailLists.segment.subscribers');
                Route::get('/details', ['\\' . EditSegmentController::class, 'edit'])->name('mailcoach.emailLists.segment.edit');
                Route::put('/details', ['\\' . EditSegmentController::class, 'update']);
                Route::post('duplicate', '\\' . DuplicateSegmentController::class)->name('mailcoach.emailLists.segment.duplicate');
            });
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('automations', \Spatie\Mailcoach\Http\App\Livewire\Automations\Automations::class))->name('mailcoach.automations');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', ['\\' . AutomationSettingsController::class, 'edit'])->name('mailcoach.automations.settings');
        Route::put('settings', ['\\' . AutomationSettingsController::class, 'update']);
        Route::get('run', ['\\' . RunAutomationController::class, 'edit'])->name('mailcoach.automations.run');
        Route::put('run', ['\\' . RunAutomationController::class, 'update']);

        Route::delete('/', '\\' . DestroyAutomationController::class)->name('mailcoach.automations.delete');
        Route::post('duplicate', '\\' . DuplicateAutomationController::class)->name('mailcoach.automations.duplicate');

        Route::prefix('actions')->group(function () {
            Route::get('/', ['\\' . AutomationActionsController::class, 'index'])->name('mailcoach.automations.actions');
            Route::post('/', ['\\' . AutomationActionsController::class, 'store'])->name('mailcoach.automations.actions.store');
        });
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('automation-mails', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMails::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\' . AutomationMailSummaryController::class)->name('mailcoach.automations.mails.summary');
        Route::post('duplicate', '\\' . DuplicateAutomationMailController::class)->name('mailcoach.automations.mails.duplicate');
        Route::delete('/', '\\' . DestroyAutomationMailController::class)->name('mailcoach.automations.mails.delete');
        Route::get('settings', ['\\' . AutomationMailSettingsController::class, 'edit'])->name('mailcoach.automations.mails.settings');
        Route::put('settings', ['\\' . AutomationMailSettingsController::class, 'update']);
        Route::get('opens', '\\' . Config::getLivewireComponentClass('automation-mail-opens', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpens::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\' . Config::getLivewireComponentClass('automation-mail-clicks', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicks::class))->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\' . Config::getLivewireComponentClass('automation-mail-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribes::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\' . Config::getLivewireComponentClass('automation-mail-outbox', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutbox::class))->name('mailcoach.automations.mails.outbox');
        Route::get('content', [AutomationMailContentController::class, 'edit'])->name('mailcoach.automations.mails.content');
        Route::put('content', [AutomationMailContentController::class, 'update'])->name('mailcoach.automations.mails.updateContent');
        Route::get('delivery', '\\' . AutomationMailDeliveryController::class)->name('mailcoach.automations.mails.delivery');
        Route::post('send-test-email', '\\' . SendAutomationMailTestController::class)->name('mailcoach.automations.mails.sendTestEmail');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('transactional-mails', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMails::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\' . TransactionalMailContentController::class)->name('mailcoach.transactionalMail.show');
        Route::get('body', '\\' . ShowTransactionalMailBodyController::class)->name('mailcoach.transactionalMail.body');
        Route::get('performance', '\\' . TransactionalMailPerformanceController::class)->name('mailcoach.transactionalMail.performance');
        Route::get('resend', [ResendTransactionalMailController::class, 'show'])->name('mailcoach.transactionalMail.resend');
        Route::post('resend', [ResendTransactionalMailController::class, 'resend']);
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('transactional-mail-templates', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailTemplates::class))->name('mailcoach.transactionalMails.templates');

    Route::prefix('{transactionalMailTemplate}')->group(function () {
        Route::get('content', ['\\' . TransactionalMailTemplatesController::class, 'edit'])->name('mailcoach.transactionalMails.templates.edit');
        Route::put('content', ['\\' . TransactionalMailTemplatesController::class, 'update']);
        Route::post('duplicate', ['\\' . TransactionalMailTemplatesController::class, 'duplicate'])->name('mailcoach.transactionalMails.templates.duplicate');

        Route::get('settings', ['\\' . TransactionalMailSettingsController::class, 'edit'])->name('mailcoach.transactionalMails.templates.settings');
        Route::put('settings', ['\\' . TransactionalMailSettingsController::class, 'update']);
        Route::post('send-test-email', '\\' . SendTransactionalMailTestController::class)->name('mailcoach.transactionalMails.templates.sendTestEmail');
    });
});

Route::prefix('subscriber-import')->group(function () {
    Route::get('{subscriberImport}/download-attachment/{collection}', '\\' . DownloadSubscriberImportAttachmentController::class)->name('mailcoach.subscriberImport.downloadAttachment');
    Route::delete('{subscriberImport}', '\\' . DestroySubscriberImportController::class)->name('mailcoach.subscriberImport.delete');
});

Route::prefix('templates')->group(function () {
    Route::get('/', '\\' . Config::getLivewireComponentClass('templates', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Templates::class))->name('mailcoach.templates');

    Route::prefix('{template}')->group(function () {
        Route::get('/', ['\\' . TemplatesController::class, 'edit'])->name('mailcoach.templates.edit');
        Route::put('/', ['\\' . TemplatesController::class, 'update']);
        Route::post('duplicate', ['\\' . TemplatesController::class, 'duplicate'])->name('mailcoach.templates.duplicate');
    });
});
