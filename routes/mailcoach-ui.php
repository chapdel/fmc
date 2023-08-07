<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\UploadsController;
use Spatie\Mailcoach\Http\App\Middleware\BootstrapSettingsNavigation;
use Spatie\Mailcoach\Livewire\Editor\EditorSettingsComponent;
use Spatie\Mailcoach\Livewire\Export\ExportComponent;
use Spatie\Mailcoach\Livewire\GeneralSettingsComponent;
use Spatie\Mailcoach\Livewire\Import\ImportComponent;
use Spatie\Mailcoach\Livewire\Mailers\EditMailerComponent;
use Spatie\Mailcoach\Livewire\Mailers\MailersComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;
use Spatie\Mailcoach\Mailcoach;

Route::get('dashboard', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Dashboard\DashboardComponent::class))->name('mailcoach.dashboard');
Route::get('debug', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\DebugComponent::class))->name('debug');

Route::post('uploads', UploadsController::class);

Route::get('export', '\\'.ExportComponent::class)->name('export');
Route::get('import', '\\'.ImportComponent::class)->name('import');

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent::class))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignSettingsComponent::class))->name('mailcoach.campaigns.settings');
        Route::get('content', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignContentComponent::class))->name('mailcoach.campaigns.content');
        Route::get('delivery', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignDeliveryComponent::class))->name('mailcoach.campaigns.delivery');

        Route::get('summary', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignSummaryComponent::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignOpensComponent::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignClicksComponent::class))->name('mailcoach.campaigns.clicks');
        Route::get('clicks/{campaignLink}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignLinkClicksComponent::class))->name('mailcoach.campaigns.link-clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignUnsubscribesComponent::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Campaigns\CampaignOutboxComponent::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\ListsComponent::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\ListSummaryComponent::class))->name('mailcoach.emailLists.summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\SubscribersComponent::class))->name('mailcoach.emailLists.subscribers');
            Route::get('{subscriber}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\SubscriberComponent::class))->name('mailcoach.emailLists.subscriber.details');
        });

        Route::get('import-subscribers', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent::class))->name('mailcoach.emailLists.import-subscribers');
        Route::get('subscriber-exports', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\SubscriberExportsComponent::class))->name('mailcoach.emailLists.subscriber-exports');

        Route::get('settings', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\ListSettingsComponent::class))->name('mailcoach.emailLists.general-settings');
        Route::get('onboarding', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\ListOnboardingComponent::class))->name('mailcoach.emailLists.onboarding');
        Route::get('mailers', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\ListMailersComponent::class))->name('mailcoach.emailLists.mailers');

        if (config('mailcoach.audience.website', true)) {
            Route::get('website', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\WebsiteComponent::class))->name('mailcoach.emailLists.website');
        }

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\TagsComponent::class))->name('mailcoach.emailLists.tags');
            Route::get('{tag}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\TagComponent::class))->name('mailcoach.emailLists.tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\SegmentsComponent::class))->name('mailcoach.emailLists.segments');
            Route::get('{segment}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Audience\SegmentComponent::class))->name('mailcoach.emailLists.segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationsComponent::class))->name('mailcoach.automations');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationSettingsComponent::class))->name('mailcoach.automations.settings');
        Route::get('run', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\RunAutomationComponent::class))->name('mailcoach.automations.run');
        Route::get('actions', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationActionsComponent::class))->name('mailcoach.automations.actions');
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailsComponent::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailSummaryComponent::class))->name('mailcoach.automations.mails.summary');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailSettingsComponent::class))->name('mailcoach.automations.mails.settings');
        Route::get('content', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailContentComponent::class))->name('mailcoach.automations.mails.content');
        Route::get('delivery', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailDeliveryComponent::class))->name('mailcoach.automations.mails.delivery');

        Route::get('opens', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailOpensComponent::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailClicksComponent::class))->name('mailcoach.automations.mails.clicks');
        Route::get('clicks/{automationMailLink}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailLinkClicksComponent::class))->name('mailcoach.automations.mails.link-clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailUnsubscribesComponent::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Automations\AutomationMailOutboxComponent::class))->name('mailcoach.automations.mails.outbox');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailLogItemsComponent::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailContentComponent::class))->name('mailcoach.transactionalMails.show');
        Route::get('performance', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailPerformanceComponent::class))->name('mailcoach.transactionalMails.performance');
        Route::get('resend', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailResendComponent::class))->name('mailcoach.transactionalMails.resend');
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailsComponent::class))->name('mailcoach.transactionalMails.templates');

    Route::prefix('{transactionalMailTemplate}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateContentComponent::class))->name('mailcoach.transactionalMails.templates.edit');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent::class))->name('mailcoach.transactionalMails.templates.settings');
    });
});

Route::prefix('templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass(TemplatesComponent::class))->name('mailcoach.templates');
    Route::get('{template}', '\\'.Mailcoach::getLivewireClass(TemplateComponent::class))->name('mailcoach.templates.edit');
});

Route::prefix('settings')
    ->middleware([BootstrapSettingsNavigation::class])
    ->group(function () {
        Route::get('general', GeneralSettingsComponent::class)->name('general-settings');

        Route::prefix('mailers')->group(function () {
            Route::get('/', MailersComponent::class)->name('mailers');
            Route::get('{mailer}', EditMailerComponent::class)->name('mailers.edit');
        });

        Route::get('editor', EditorSettingsComponent::class)->name('editor');

        Route::prefix('webhooks')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent::class))->name('webhooks');
            Route::get('{webhook}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Webhooks\EditWebhookComponent::class))->name('webhooks.edit');
            Route::get('{webhook}/logs/{webhookLog}', '\\'.Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\Webhooks\WebhookLogComponent::class))->name('webhooks.logs.show');
        });
    });
