<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailDeliveryController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailSummaryController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\DestroyAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\DuplicateAutomationMailController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\SendAutomationMailTestController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignTestController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\TemplatesController;
use Spatie\Mailcoach\Http\App\Controllers\DebugController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\SubscribersExportController;
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
    Route::get('/', '\\' . Config::getLivewireClass('campaigns', Config::getLivewireClass('campaigns', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns::class)))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', Config::getLivewireClass('campaign-settings', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettings::class))->name('mailcoach.campaigns.settings');
        Route::get('content', ['\\' . CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
        Route::get('delivery', Config::getLivewireClass('campaign-delivery', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDelivery::class))->name('mailcoach.campaigns.delivery');

        Route::middleware('\\' . EditableCampaign::class)->group(function () {
            Route::put('content', ['\\' . CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');

            Route::post('send-test-email', '\\' . SendCampaignTestController::class)->name('mailcoach.campaigns.sendTestEmail');
        });

        Route::get('summary', '\\' . Config::getLivewireClass('campaign-summary', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummary::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\' . Config::getLivewireClass('campaign-opens', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpens::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\' . Config::getLivewireClass('campaign-links', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicks::class))->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\' . Config::getLivewireClass('campaign-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribes::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\' . Config::getLivewireClass('campaign-outbox', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutbox::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\' . Config::getLivewireClass('lists', \Spatie\Mailcoach\Http\App\Livewire\Audience\Lists::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\' . Config::getLivewireClass('list-summary', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummary::class))->name('mailcoach.emailLists.summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', '\\' . Config::getLivewireClass('subscribers', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscribers::class))->name('mailcoach.emailLists.subscribers');
            Route::post('export', '\\' . SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');
            Route::get('{subscriber}', '\\' . Config::getLivewireClass('subscriber', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscriber::class))->name('mailcoach.emailLists.subscriber.details');
        });

        Route::get('import-subscribers', ['\\' . ImportSubscribersController::class, 'showImportScreen'])->name('mailcoach.emailLists.import-subscribers');
        Route::post('import-subscribers', ['\\' . ImportSubscribersController::class, 'import']);

        Route::get('settings', '\\' . Config::getLivewireClass('list-settings', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettings::class))->name('mailcoach.emailLists.general-settings');
        Route::get('onboarding', '\\' . Config::getLivewireClass('list-onboarding', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboarding::class))->name('mailcoach.emailLists.onboarding');
        Route::get('mailers', '\\' . Config::getLivewireClass('list-mailers', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailers::class))->name('mailcoach.emailLists.mailers');

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\' . Config::getLivewireClass('tags', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tags::class))->name('mailcoach.emailLists.tags');
            Route::get('{tag}', '\\' . Config::getLivewireClass('tag', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tag::class))->name('mailcoach.emailLists.tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\' . Config::getLivewireClass('segments', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segments::class))->name('mailcoach.emailLists.segments');
            Route::get('{segment}', '\\' . Config::getLivewireClass('segment', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segment::class))->name('mailcoach.emailLists.segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\' . Config::getLivewireClass('automations', \Spatie\Mailcoach\Http\App\Livewire\Automations\Automations::class))->name('mailcoach.automations');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', '\\' . Config::getLivewireClass('automation-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettings::class))->name('mailcoach.automations.settings');
        Route::get('run', '\\' . Config::getLivewireClass('automation-run', \Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomation::class))->name('mailcoach.automations.run');
        Route::get('actions', '\\' . Config::getLivewireClass('automation-actions', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActions::class))->name('mailcoach.automations.actions');
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\' . Config::getLivewireClass('automation-mails', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMails::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\' . AutomationMailSummaryController::class)->name('mailcoach.automations.mails.summary');
        Route::post('duplicate', '\\' . DuplicateAutomationMailController::class)->name('mailcoach.automations.mails.duplicate');
        Route::delete('/', '\\' . DestroyAutomationMailController::class)->name('mailcoach.automations.mails.delete');
        Route::get('settings', ['\\' . AutomationMailSettingsController::class, 'edit'])->name('mailcoach.automations.mails.settings');
        Route::put('settings', ['\\' . AutomationMailSettingsController::class, 'update']);
        Route::get('opens', '\\' . Config::getLivewireClass('automation-mail-opens', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpens::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\' . Config::getLivewireClass('automation-mail-clicks', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicks::class))->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\' . Config::getLivewireClass('automation-mail-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribes::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\' . Config::getLivewireClass('automation-mail-outbox', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutbox::class))->name('mailcoach.automations.mails.outbox');
        Route::get('content', [AutomationMailContentController::class, 'edit'])->name('mailcoach.automations.mails.content');
        Route::put('content', [AutomationMailContentController::class, 'update'])->name('mailcoach.automations.mails.updateContent');
        Route::get('delivery', '\\' . AutomationMailDeliveryController::class)->name('mailcoach.automations.mails.delivery');
        Route::post('send-test-email', '\\' . SendAutomationMailTestController::class)->name('mailcoach.automations.mails.sendTestEmail');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\' . Config::getLivewireClass('transactional-mails', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMails::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\' . TransactionalMailContentController::class)->name('mailcoach.transactionalMail.show');
        Route::get('body', '\\' . ShowTransactionalMailBodyController::class)->name('mailcoach.transactionalMail.body');
        Route::get('performance', '\\' . TransactionalMailPerformanceController::class)->name('mailcoach.transactionalMail.performance');
        Route::get('resend', [ResendTransactionalMailController::class, 'show'])->name('mailcoach.transactionalMail.resend');
        Route::post('resend', [ResendTransactionalMailController::class, 'resend']);
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\' . Config::getLivewireClass('transactional-mail-templates', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailTemplates::class))->name('mailcoach.transactionalMails.templates');

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
    Route::get('/', '\\' . Config::getLivewireClass('templates', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Templates::class))->name('mailcoach.templates');

    Route::prefix('{template}')->group(function () {
        Route::get('/', ['\\' . TemplatesController::class, 'edit'])->name('mailcoach.templates.edit');
        Route::put('/', ['\\' . TemplatesController::class, 'update']);
        Route::post('duplicate', ['\\' . TemplatesController::class, 'duplicate'])->name('mailcoach.templates.duplicate');
    });
});
