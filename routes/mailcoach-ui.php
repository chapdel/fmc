<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\UploadsController;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\SendAutomationMailTestController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignTestController;
use Spatie\Mailcoach\Http\App\Controllers\DebugController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\SubscribersExportController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DestroySubscriberImportController;
use Spatie\Mailcoach\Http\App\Controllers\SubscriberImports\DownloadSubscriberImportAttachmentController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\SendTransactionalMailTestController;
use Spatie\Mailcoach\Http\App\Middleware\EditableCampaign;

Route::get('dashboard', Mailcoach::getLivewireClass('dashboard', \Spatie\Mailcoach\Http\App\Livewire\Dashboard::class))->name('mailcoach.dashboard');
Route::get('debug', '\\' . DebugController::class)->name('debug');

Route::post('uploads', UploadsController::class);

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('campaigns', Mailcoach::getLivewireClass('campaigns', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns::class)))->name('mailcoach.campaigns');

    Route::prefix('{campaign:uuid}')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass('campaign-settings', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettings::class))->name('mailcoach.campaigns.settings');
        Route::get('content', ['\\' . CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
        Route::get('delivery', Mailcoach::getLivewireClass('campaign-delivery', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDelivery::class))->name('mailcoach.campaigns.delivery');

        Route::middleware('\\' . EditableCampaign::class)->group(function () {
            Route::put('content', ['\\' . CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');
        });

        Route::get('summary', '\\' . Mailcoach::getLivewireClass('campaign-summary', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummary::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\' . Mailcoach::getLivewireClass('campaign-opens', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpens::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\' . Mailcoach::getLivewireClass('campaign-links', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicks::class))->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\' . Mailcoach::getLivewireClass('campaign-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribes::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\' . Mailcoach::getLivewireClass('campaign-outbox', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutbox::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('lists', \Spatie\Mailcoach\Http\App\Livewire\Audience\Lists::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList:uuid}')->group(function () {
        Route::get('summary', '\\' . Mailcoach::getLivewireClass('list-summary', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummary::class))->name('mailcoach.emailLists.summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', '\\' . Mailcoach::getLivewireClass('subscribers', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscribers::class))->name('mailcoach.emailLists.subscribers');
            Route::post('export', '\\' . SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');
            Route::get('{subscriber:uuid}', '\\' . Mailcoach::getLivewireClass('subscriber', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscriber::class))->name('mailcoach.emailLists.subscriber.details');
        });

        Route::get('import-subscribers', ['\\' . ImportSubscribersController::class, 'showImportScreen'])->name('mailcoach.emailLists.import-subscribers');
        Route::post('import-subscribers', ['\\' . ImportSubscribersController::class, 'import']);

        Route::get('settings', '\\' . Mailcoach::getLivewireClass('list-settings', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettings::class))->name('mailcoach.emailLists.general-settings');
        Route::get('onboarding', '\\' . Mailcoach::getLivewireClass('list-onboarding', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboarding::class))->name('mailcoach.emailLists.onboarding');
        Route::get('mailers', '\\' . Mailcoach::getLivewireClass('list-mailers', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailers::class))->name('mailcoach.emailLists.mailers');

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\' . Mailcoach::getLivewireClass('tags', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tags::class))->name('mailcoach.emailLists.tags');
            Route::get('{tag:uuid}', '\\' . Mailcoach::getLivewireClass('tag', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tag::class))->name('mailcoach.emailLists.tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\' . Mailcoach::getLivewireClass('segments', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segments::class))->name('mailcoach.emailLists.segments');
            Route::get('{segment:uuid}', '\\' . Mailcoach::getLivewireClass('segment', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segment::class))->name('mailcoach.emailLists.segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('automations', \Spatie\Mailcoach\Http\App\Livewire\Automations\Automations::class))->name('mailcoach.automations');

    Route::prefix('{automation:uuid}')->group(function () {
        Route::get('settings', '\\' . Mailcoach::getLivewireClass('automation-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettings::class))->name('mailcoach.automations.settings');
        Route::get('run', '\\' . Mailcoach::getLivewireClass('automation-run', \Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomation::class))->name('mailcoach.automations.run');
        Route::get('actions', '\\' . Mailcoach::getLivewireClass('automation-actions', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActions::class))->name('mailcoach.automations.actions');
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('automation-mails', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMails::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail:uuid}')->group(function () {
        Route::get('summary', '\\' . Mailcoach::getLivewireClass('automation-mail-summary', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSummary::class))->name('mailcoach.automations.mails.summary');
        Route::get('settings', '\\' . Mailcoach::getLivewireClass('automation-mail-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSettings::class))->name('mailcoach.automations.mails.settings');
        Route::get('delivery', '\\' . Mailcoach::getLivewireClass('automation-mail-delivery', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailDelivery::class))->name('mailcoach.automations.mails.delivery');

        Route::get('opens', '\\' . Mailcoach::getLivewireClass('automation-mail-opens', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpens::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\' . Mailcoach::getLivewireClass('automation-mail-clicks', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicks::class))->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\' . Mailcoach::getLivewireClass('automation-mail-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribes::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\' . Mailcoach::getLivewireClass('automation-mail-outbox', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutbox::class))->name('mailcoach.automations.mails.outbox');

        Route::get('content', [AutomationMailContentController::class, 'edit'])->name('mailcoach.automations.mails.content');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('transactional-mails', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMails::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail:uuid}')->group(function () {
        Route::get('content', '\\' . Mailcoach::getLivewireClass('transactional-mail-content', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailContent::class))->name('mailcoach.transactionalMails.show');
        Route::get('performance', '\\' . Mailcoach::getLivewireClass('transactional-mail-performance', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailPerformance::class))->name('mailcoach.transactionalMails.performance');
        Route::get('resend', '\\' . Mailcoach::getLivewireClass('transactional-mail-resend', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailResend::class))->name('mailcoach.transactionalMails.resend');
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('transactional-mail-templates', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplates::class))->name('mailcoach.transactionalMails.templates');

    Route::prefix('{template:uuid}')->group(function () {
        Route::get('content', '\\' . Mailcoach::getLivewireClass('transactional-mail-template-content', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateContent::class))->name('mailcoach.transactionalMails.templates.edit');
        Route::get('settings', '\\' . Mailcoach::getLivewireClass('transactional-mail-template-settings', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateSettings::class))->name('mailcoach.transactionalMails.templates.settings');

        Route::post('send-test-email', '\\' . SendTransactionalMailTestController::class)->name('mailcoach.transactionalMails.templates.sendTestEmail');
    });
});

Route::prefix('subscriber-import')->group(function () {
    Route::get('{subscriberImport:uuid}/download-attachment/{collection}', '\\' . DownloadSubscriberImportAttachmentController::class)->name('mailcoach.subscriberImport.downloadAttachment');
    Route::delete('{subscriberImport:uuid}', '\\' . DestroySubscriberImportController::class)->name('mailcoach.subscriberImport.delete');
});

Route::prefix('templates')->group(function () {
    Route::get('/', '\\' . Mailcoach::getLivewireClass('templates', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Templates::class))->name('mailcoach.templates');
    Route::get('{template:uuid}', '\\' . Mailcoach::getLivewireClass('template', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Template::class))->name('mailcoach.templates.edit');
});
