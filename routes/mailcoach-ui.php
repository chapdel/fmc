<?php

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\Api\Controllers\UploadsController;
use Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails\AutomationMailContentController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Http\App\Controllers\DebugController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\SubscribersExportController;
use Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates\SendTransactionalMailTestController;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Template;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Templates;
use Spatie\Mailcoach\Http\App\Livewire\Export\Export;
use Spatie\Mailcoach\Http\App\Livewire\Import\Import;
use Spatie\Mailcoach\Http\App\Middleware\BootstrapSettingsNavigation;
use Spatie\Mailcoach\Http\App\Middleware\EditableCampaign;
use Spatie\Mailcoach\Http\Auth\Controllers\LogoutController;
use Spatie\Mailcoach\Http\Livewire\EditMailer;
use Spatie\Mailcoach\Http\Livewire\EditorSettings;
use Spatie\Mailcoach\Http\Livewire\EditUser;
use Spatie\Mailcoach\Http\Livewire\EditWebhook;
use Spatie\Mailcoach\Http\Livewire\GeneralSettings;
use Spatie\Mailcoach\Http\Livewire\Mailers;
use Spatie\Mailcoach\Http\Livewire\Password;
use Spatie\Mailcoach\Http\Livewire\Profile;
use Spatie\Mailcoach\Http\Livewire\Tokens;
use Spatie\Mailcoach\Http\Livewire\Users;
use Spatie\Mailcoach\Http\Livewire\Webhooks;
use Spatie\Mailcoach\Mailcoach;

Route::get('dashboard', Mailcoach::getLivewireClass('dashboard', \Spatie\Mailcoach\Http\App\Livewire\Dashboard::class))->name('mailcoach.dashboard');
Route::get('debug', '\\'.DebugController::class)->name('debug');

Route::post('uploads', UploadsController::class);

Route::get('export', '\\'.Export::class)->name('export');
Route::get('import', '\\'.Import::class)->name('import');

Route::prefix('campaigns')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('campaigns', Mailcoach::getLivewireClass('campaigns', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns::class)))->name('mailcoach.campaigns');

    Route::prefix('{campaign}')->group(function () {
        Route::get('settings', Mailcoach::getLivewireClass('campaign-settings', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettings::class))->name('mailcoach.campaigns.settings');
        Route::get('content', ['\\'.CampaignContentController::class, 'edit'])->name('mailcoach.campaigns.content');
        Route::get('delivery', Mailcoach::getLivewireClass('campaign-delivery', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDelivery::class))->name('mailcoach.campaigns.delivery');

        Route::middleware('\\'.EditableCampaign::class)->group(function () {
            Route::put('content', ['\\'.CampaignContentController::class, 'update'])->name('mailcoach.campaigns.updateContent');
        });

        Route::get('summary', '\\'.Mailcoach::getLivewireClass('campaign-summary', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummary::class))->name('mailcoach.campaigns.summary');
        Route::get('opens', '\\'.Mailcoach::getLivewireClass('campaign-opens', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpens::class))->name('mailcoach.campaigns.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass('campaign-links', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicks::class))->name('mailcoach.campaigns.clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass('campaign-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribes::class))->name('mailcoach.campaigns.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass('campaign-outbox', \Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutbox::class))->name('mailcoach.campaigns.outbox');
    });
});

Route::prefix('email-lists')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('lists', \Spatie\Mailcoach\Http\App\Livewire\Audience\Lists::class))->name('mailcoach.emailLists');

    Route::prefix('{emailList}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass('list-summary', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummary::class))->name('mailcoach.emailLists.summary');

        Route::prefix('subscribers')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('subscribers', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscribers::class))->name('mailcoach.emailLists.subscribers');
            Route::post('export', '\\'.SubscribersExportController::class)->name('mailcoach.emailLists.subscribers.export');
            Route::get('{subscriber}', '\\'.Mailcoach::getLivewireClass('subscriber', \Spatie\Mailcoach\Http\App\Livewire\Audience\Subscriber::class))->name('mailcoach.emailLists.subscriber.details');
        });

        Route::get('import-subscribers', '\\'.Mailcoach::getLivewireClass('subscriber-imports', \Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberImports::class))->name('mailcoach.emailLists.import-subscribers');

        Route::get('settings', '\\'.Mailcoach::getLivewireClass('list-settings', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettings::class))->name('mailcoach.emailLists.general-settings');
        Route::get('onboarding', '\\'.Mailcoach::getLivewireClass('list-onboarding', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboarding::class))->name('mailcoach.emailLists.onboarding');
        Route::get('mailers', '\\'.Mailcoach::getLivewireClass('list-mailers', \Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailers::class))->name('mailcoach.emailLists.mailers');
        Route::get('website', '\\'.Mailcoach::getLivewireClass('list-website', \Spatie\Mailcoach\Http\App\Livewire\Audience\Website::class))->name('mailcoach.emailLists.website');

        Route::prefix('tags')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('tags', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tags::class))->name('mailcoach.emailLists.tags');
            Route::get('{tag}', '\\'.Mailcoach::getLivewireClass('tag', \Spatie\Mailcoach\Http\App\Livewire\Audience\Tag::class))->name('mailcoach.emailLists.tags.edit');
        });

        Route::prefix('segments')->group(function () {
            Route::get('/', '\\'.Mailcoach::getLivewireClass('segments', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segments::class))->name('mailcoach.emailLists.segments');
            Route::get('{segment}', '\\'.Mailcoach::getLivewireClass('segment', \Spatie\Mailcoach\Http\App\Livewire\Audience\Segment::class))->name('mailcoach.emailLists.segments.edit');
        });
    });
});

Route::prefix('automations')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('automations', \Spatie\Mailcoach\Http\App\Livewire\Automations\Automations::class))->name('mailcoach.automations');

    Route::prefix('{automation}')->group(function () {
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('automation-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettings::class))->name('mailcoach.automations.settings');
        Route::get('run', '\\'.Mailcoach::getLivewireClass('automation-run', \Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomation::class))->name('mailcoach.automations.run');
        Route::get('actions', '\\'.Mailcoach::getLivewireClass('automation-actions', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActions::class))->name('mailcoach.automations.actions');
    });
});

Route::prefix('automation-emails')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('automation-mails', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMails::class))->name('mailcoach.automations.mails');

    Route::prefix('{automationMail}')->group(function () {
        Route::get('summary', '\\'.Mailcoach::getLivewireClass('automation-mail-summary', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSummary::class))->name('mailcoach.automations.mails.summary');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('automation-mail-settings', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSettings::class))->name('mailcoach.automations.mails.settings');
        Route::get('delivery', '\\'.Mailcoach::getLivewireClass('automation-mail-delivery', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailDelivery::class))->name('mailcoach.automations.mails.delivery');

        Route::get('opens', '\\'.Mailcoach::getLivewireClass('automation-mail-opens', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpens::class))->name('mailcoach.automations.mails.opens');
        Route::get('clicks', '\\'.Mailcoach::getLivewireClass('automation-mail-clicks', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicks::class))->name('mailcoach.automations.mails.clicks');
        Route::get('unsubscribes', '\\'.Mailcoach::getLivewireClass('automation-mail-unsubscribes', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribes::class))->name('mailcoach.automations.mails.unsubscribes');
        Route::get('outbox', '\\'.Mailcoach::getLivewireClass('automation-mail-outbox', \Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutbox::class))->name('mailcoach.automations.mails.outbox');

        Route::get('content', [AutomationMailContentController::class, 'edit'])->name('mailcoach.automations.mails.content');
    });
});

Route::prefix('transactional-mail-log')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('transactional-mails', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailLogItems::class))->name('mailcoach.transactionalMails');

    Route::prefix('{transactionalMail}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass('transactional-mail-content', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailContent::class))->name('mailcoach.transactionalMails.show');
        Route::get('performance', '\\'.Mailcoach::getLivewireClass('transactional-mail-performance', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailPerformance::class))->name('mailcoach.transactionalMails.performance');
        Route::get('resend', '\\'.Mailcoach::getLivewireClass('transactional-mail-resend', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailResend::class))->name('mailcoach.transactionalMails.resend');
    });
});

Route::prefix('transactional-mail-templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('transactional-mail-templates', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMails::class))->name('mailcoach.transactionalMails.templates');

    Route::prefix('{transactionalMailTemplate}')->group(function () {
        Route::get('content', '\\'.Mailcoach::getLivewireClass('transactional-mail-template-content', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateContent::class))->name('mailcoach.transactionalMails.templates.edit');
        Route::get('settings', '\\'.Mailcoach::getLivewireClass('transactional-mail-template-settings', \Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateSettings::class))->name('mailcoach.transactionalMails.templates.settings');

        Route::post('send-test-email', '\\'.SendTransactionalMailTestController::class)->name('mailcoach.transactionalMails.templates.sendTestEmail');
    });
});

Route::prefix('templates')->group(function () {
    Route::get('/', '\\'.Mailcoach::getLivewireClass('templates', Templates::class))->name('mailcoach.templates');
    Route::get('{template}', '\\'.Mailcoach::getLivewireClass('template', Template::class))->name('mailcoach.templates.edit');
});

Route::prefix('settings')
    ->middleware([BootstrapSettingsNavigation::class])
    ->group(function () {
        Route::get('general', GeneralSettings::class)->name('general-settings');

        Route::prefix('account')->group(function () {
            Route::get('details', Profile::class)->name('account');

            Route::get('password', Password::class)->name('password');

            Route::prefix('tokens')->group(function () {
                Route::get('/', Tokens::class)->name('tokens');
            });
        });

        Route::prefix('mailers')->group(function () {
            Route::get('/', Mailers::class)->name('mailers');
            Route::get('{mailer}', EditMailer::class)->name('mailers.edit');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', Users::class)->name('users');
            Route::get('{user}', EditUser::class)->name('users.edit');
        });

        Route::get('editor', EditorSettings::class)->name('editor');

        Route::prefix('webhooks')->group(function () {
            Route::get('/', Webhooks::class)->name('webhooks');
            Route::get('{webhook}', EditWebhook::class)->name('webhooks.edit');
        });
    });

Route::post('logout', LogoutController::class)->name('mailcoach.logout');
