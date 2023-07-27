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
use Spatie\Mailcoach\Livewire\PasswordComponent;
use Spatie\Mailcoach\Livewire\ProfileComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;
use Spatie\Mailcoach\Livewire\TokensComponent;
use Spatie\Mailcoach\Livewire\Users\EditUserComponent;
use Spatie\Mailcoach\Livewire\Users\UsersComponent;
use Spatie\Mailcoach\Mailcoach;

Route::get('dashboard', Mailcoach::getLivewireClass('dashboard', \Spatie\Mailcoach\Livewire\Dashboard\DashboardComponent::class))->name('mailcoach.dashboard');
Route::get('debug', Mailcoach::getLivewireClass('debug', \Spatie\Mailcoach\Livewire\DebugComponent::class))->name('debug');

Route::post('uploads', UploadsController::class);

Route::get('export', '\\'.ExportComponent::class)->name('export');
Route::get('import', '\\'.ImportComponent::class)->name('import');

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('campaigns', Mailcoach::getLivewireClass('campaigns', \Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent::class)))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass('campaign-settings', \Spatie\Mailcoach\Livewire\Campaigns\CampaignSettingsComponent::class))->name('mailcoach.campaigns.settings');
        Route::get('content', Mailcoach::getLivewireClass('campaign-content', \Spatie\Mailcoach\Livewire\Campaigns\CampaignContentComponent::class))->name('mailcoach.campaigns.content');
        Route::get('delivery', Mailcoach::getLivewireClass('campaign-delivery', \Spatie\Mailcoach\Livewire\Campaigns\CampaignDeliveryComponent::class))->name('mailcoach.campaigns.delivery');

        Route::get('summary', '\\'.Mailcoach::getLivewireClass('campaign-summary', \Spatie\Mailcoach\Livewire\Campaigns\CampaignSummaryComponent::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\'.Mailcoach::getLivewireClass('campaign-opens', \Spatie\Mailcoach\Livewire\Campaigns\CampaignOpensComponent::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass('campaign-links', \Spatie\Mailcoach\Livewire\Campaigns\CampaignClicksComponent::class))->name('mailcoach.campaigns.clicks');
        Route::get('clicks/{campaignLink}', '\\'.Mailcoach::getLivewireClass('campaign-link-clicks', \Spatie\Mailcoach\Livewire\Campaigns\CampaignLinkClicksComponent::class))->name('mailcoach.campaigns.link-clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass('campaign-unsubscribes', \Spatie\Mailcoach\Livewire\Campaigns\CampaignUnsubscribesComponent::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass('campaign-outbox', \Spatie\Mailcoach\Livewire\Campaigns\CampaignOutboxComponent::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('lists', \Spatie\Mailcoach\Livewire\Audience\ListsComponent::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass('list-summary', \Spatie\Mailcoach\Livewire\Audience\ListSummaryComponent::class))->name('mailcoach.emailLists.summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('subscribers', \Spatie\Mailcoach\Livewire\Audience\SubscribersComponent::class))->name('mailcoach.emailLists.subscribers');
            Route::get('{subscriber}', '\\'.Mailcoach::getLivewireClass('subscriber', \Spatie\Mailcoach\Livewire\Audience\SubscriberComponent::class))->name('mailcoach.emailLists.subscriber.details');
        });

        Route::get('import-subscribers', '\\'.Mailcoach::getLivewireClass('subscriber-imports', \Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent::class))->name('mailcoach.emailLists.import-subscribers');

        Route::get('settings', '\\'.Mailcoach::getLivewireClass('list-settings', \Spatie\Mailcoach\Livewire\Audience\ListSettingsComponent::class))->name('mailcoach.emailLists.general-settings');
        Route::get('onboarding', '\\'.Mailcoach::getLivewireClass('list-onboarding', \Spatie\Mailcoach\Livewire\Audience\ListOnboardingComponent::class))->name('mailcoach.emailLists.onboarding');
        Route::get('mailers', '\\'.Mailcoach::getLivewireClass('list-mailers', \Spatie\Mailcoach\Livewire\Audience\ListMailersComponent::class))->name('mailcoach.emailLists.mailers');

        if (config('mailcoach.audience.website', true)) {
            Route::get('website', '\\'.Mailcoach::getLivewireClass('list-website', \Spatie\Mailcoach\Livewire\Audience\WebsiteComponent::class))->name('mailcoach.emailLists.website');
        }

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('tags', \Spatie\Mailcoach\Livewire\Audience\TagsComponent::class))->name('mailcoach.emailLists.tags');
            Route::get('{tag}', '\\'.Mailcoach::getLivewireClass('tag', \Spatie\Mailcoach\Livewire\Audience\TagComponent::class))->name('mailcoach.emailLists.tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('segments', \Spatie\Mailcoach\Livewire\Audience\SegmentsComponent::class))->name('mailcoach.emailLists.segments');
            Route::get('{segment}', '\\'.Mailcoach::getLivewireClass('segment', \Spatie\Mailcoach\Livewire\Audience\SegmentComponent::class))->name('mailcoach.emailLists.segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('automations', \Spatie\Mailcoach\Livewire\Automations\AutomationsComponent::class))->name('mailcoach.automations');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('automation-settings', \Spatie\Mailcoach\Livewire\Automations\AutomationSettingsComponent::class))->name('mailcoach.automations.settings');
        Route::get('run', '\\'.Mailcoach::getLivewireClass('automation-run', \Spatie\Mailcoach\Livewire\Automations\RunAutomationComponent::class))->name('mailcoach.automations.run');
        Route::get('actions', '\\'.Mailcoach::getLivewireClass('automation-actions', \Spatie\Mailcoach\Livewire\Automations\AutomationActionsComponent::class))->name('mailcoach.automations.actions');
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('automation-mails', \Spatie\Mailcoach\Livewire\Automations\AutomationMailsComponent::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass('automation-mail-summary', \Spatie\Mailcoach\Livewire\Automations\AutomationMailSummaryComponent::class))->name('mailcoach.automations.mails.summary');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('automation-mail-settings', \Spatie\Mailcoach\Livewire\Automations\AutomationMailSettingsComponent::class))->name('mailcoach.automations.mails.settings');
        Route::get('content', '\\'.Mailcoach::getLivewireClass('automation-mail-content', \Spatie\Mailcoach\Livewire\Automations\AutomationMailContentComponent::class))->name('mailcoach.automations.mails.content');
        Route::get('delivery', '\\'.Mailcoach::getLivewireClass('automation-mail-delivery', \Spatie\Mailcoach\Livewire\Automations\AutomationMailDeliveryComponent::class))->name('mailcoach.automations.mails.delivery');

        Route::get('opens', '\\'.Mailcoach::getLivewireClass('automation-mail-opens', \Spatie\Mailcoach\Livewire\Automations\AutomationMailOpensComponent::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass('automation-mail-clicks', \Spatie\Mailcoach\Livewire\Automations\AutomationMailClicksComponent::class))->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass('automation-mail-unsubscribes', \Spatie\Mailcoach\Livewire\Automations\AutomationMailUnsubscribesComponent::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass('automation-mail-outbox', \Spatie\Mailcoach\Livewire\Automations\AutomationMailOutboxComponent::class))->name('mailcoach.automations.mails.outbox');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('transactional-mails', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailLogItemsComponent::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass('transactional-mail-content', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailContentComponent::class))->name('mailcoach.transactionalMails.show');
        Route::get('performance', '\\'.Mailcoach::getLivewireClass('transactional-mail-performance', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailPerformanceComponent::class))->name('mailcoach.transactionalMails.performance');
        Route::get('resend', '\\'.Mailcoach::getLivewireClass('transactional-mail-resend', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailResendComponent::class))->name('mailcoach.transactionalMails.resend');
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('transactional-mail-templates', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailsComponent::class))->name('mailcoach.transactionalMails.templates');

    Route::prefix('{transactionalMailTemplate}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass('transactional-mail-template-content', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateContentComponent::class))->name('mailcoach.transactionalMails.templates.edit');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('transactional-mail-template-settings', \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent::class))->name('mailcoach.transactionalMails.templates.settings');
    });
});

Route::prefix('templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('templates', TemplatesComponent::class))->name('mailcoach.templates');
    Route::get('{template}', '\\'.Mailcoach::getLivewireClass('template', TemplateComponent::class))->name('mailcoach.templates.edit');
});

Route::prefix('settings')
    ->middleware([BootstrapSettingsNavigation::class])
    ->group(function () {
        Route::get('general', GeneralSettingsComponent::class)->name('general-settings');

        Route::prefix('account')->group(function () {
            Route::get('details', ProfileComponent::class)->name('account');

            Route::get('password', PasswordComponent::class)->name('password');

            Route::prefix('tokens')->group(function () {
                Route::get('/', TokensComponent::class)->name('tokens');
            });
        });

        Route::prefix('mailers')->group(function () {
            Route::get('/', MailersComponent::class)->name('mailers');
            Route::get('{mailer}', EditMailerComponent::class)->name('mailers.edit');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', UsersComponent::class)->name('users');
            Route::get('{mailcoachUser}', EditUserComponent::class)->name('users.edit');
        });

        Route::get('editor', EditorSettingsComponent::class)->name('editor');

        Route::prefix('webhooks')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('webhooks', \Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent::class))->name('webhooks');
            Route::get('{webhook}', '\\'.Mailcoach::getLivewireClass('edit-webhook', \Spatie\Mailcoach\Livewire\Webhooks\EditWebhookComponent::class))->name('webhooks.edit');
            Route::get('{webhook}/logs', '\\'.Mailcoach::getLivewireClass('webhook-logs', \Spatie\Mailcoach\Livewire\Webhooks\WebhookLogsComponent::class))->name('webhooks.logs.index');
            Route::get('{webhook}/logs/{webhookLog}', '\\'.Mailcoach::getLivewireClass('webhook-log', \Spatie\Mailcoach\Livewire\Webhooks\WebhookLogComponent::class))->name('webhooks.logs.show');
        });
    });
