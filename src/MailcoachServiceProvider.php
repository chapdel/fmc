<?php

namespace Spatie\Mailcoach;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Livewire;
use LivewireUI\Spotlight\Spotlight;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\ReplacerHelpTextsComponent;
use Spatie\Mailcoach\Domain\Audience\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Domain\Audience\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Domain\Audience\Livewire\EmailListStatistics;
use Spatie\Mailcoach\Domain\Automation\Commands\CalculateAutomationMailStatisticsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\SendAutomationMailsCommand;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Automation\Listeners\AddAutomationMailClickedTag;
use Spatie\Mailcoach\Domain\Automation\Listeners\AddAutomationMailOpenedTag;
use Spatie\Mailcoach\Domain\Automation\Models\Trigger;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AddTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AutomationMailActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\ConditionActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\RemoveTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\SendWebhookActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\SplitActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\SubscribeToEmailListActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationBuilder;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\DateTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\NoTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagAddedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagRemovedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\WebhookTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredByEvents;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignMailsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddCampaignClickedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddCampaignOpenedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SendCampaignSentEmail;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SetWebhookCallProcessedAt;
use Spatie\Mailcoach\Domain\Settings\Commands\PublishCommand;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookEventSubscriber;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookFailedAttemptsSubscriber;
use Spatie\Mailcoach\Domain\Settings\EventSubscribers\WebhookLogEventSubscriber;
use Spatie\Mailcoach\Domain\Settings\Models\MailcoachUser;
use Spatie\Mailcoach\Domain\Settings\SettingsNavigation;
use Spatie\Mailcoach\Domain\Settings\Support\AppConfiguration\AppConfiguration;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\EditorConfiguration;
use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\CleanupProcessedFeedbackCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\DeleteOldExportsCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottleCache;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Http\App\Middleware\BootstrapMailcoach;
use Spatie\Mailcoach\Http\App\ViewComposers\WebsiteStyleComposer;
use Spatie\Mailcoach\Livewire\Audience\CreateListComponent;
use Spatie\Mailcoach\Livewire\Audience\CreateSegmentComponent;
use Spatie\Mailcoach\Livewire\Audience\CreateSubscriberComponent;
use Spatie\Mailcoach\Livewire\Audience\CreateTagComponent;
use Spatie\Mailcoach\Livewire\Audience\EmailListCountComponent;
use Spatie\Mailcoach\Livewire\Audience\ListMailersComponent;
use Spatie\Mailcoach\Livewire\Audience\ListOnboardingComponent;
use Spatie\Mailcoach\Livewire\Audience\ListsComponent;
use Spatie\Mailcoach\Livewire\Audience\ListSettingsComponent;
use Spatie\Mailcoach\Livewire\Audience\ListSummaryComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentPopulationCountComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentsComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentSubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberExportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberSendsComponent;
use Spatie\Mailcoach\Livewire\Audience\TagComponent;
use Spatie\Mailcoach\Livewire\Audience\TagPopulationCountComponent;
use Spatie\Mailcoach\Livewire\Audience\TagsComponent;
use Spatie\Mailcoach\Livewire\Audience\WebsiteComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailClicksComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailContentComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailOpensComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailOutboxComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailSettingsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailSummaryComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailUnsubscribesComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationSettingsComponent;
use Spatie\Mailcoach\Livewire\Automations\CreateAutomationComponent;
use Spatie\Mailcoach\Livewire\Automations\CreateAutomationMailComponent;
use Spatie\Mailcoach\Livewire\Automations\RunAutomationComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignClicksComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignContentComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignDeliveryComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignLinkClicksComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignOpensComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignOutboxComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSettingsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignStatisticsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSummaryComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignUnsubscribesComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CreateCampaignComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\ConditionBuilderComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberAttributesConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberClickedAutomationMailLinkConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberClickedCampaignLinkConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberEmailQueryConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedAutomationMailConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberOpenedCampaignConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberSubscribedAtConditionComponent;
use Spatie\Mailcoach\Livewire\ConditionBuilder\Conditions\Subscribers\SubscriberTagsConditionComponent;
use Spatie\Mailcoach\Livewire\Dashboard\DashboardChart;
use Spatie\Mailcoach\Livewire\Dashboard\DashboardComponent;
use Spatie\Mailcoach\Livewire\Editor\EditorSettingsComponent;
use Spatie\Mailcoach\Livewire\Editor\TextAreaEditorComponent;
use Spatie\Mailcoach\Livewire\Export\ExportComponent;
use Spatie\Mailcoach\Livewire\GeneralSettingsComponent;
use Spatie\Mailcoach\Livewire\Import\ImportComponent;
use Spatie\Mailcoach\Livewire\LinkCheckComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Mailgun\MailgunSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Postmark\PostmarkSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\SendGrid\SendGridSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Sendinblue\SendinblueSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Ses\SesSetupWizardComponent;
use Spatie\Mailcoach\Livewire\MailConfiguration\Smtp\SmtpSetupWizardComponent;
use Spatie\Mailcoach\Livewire\Mailers\CreateMailerComponent;
use Spatie\Mailcoach\Livewire\Mailers\EditMailerComponent;
use Spatie\Mailcoach\Livewire\Mailers\MailersComponent;
use Spatie\Mailcoach\Livewire\Mails\CreateSuppressionComponent;
use Spatie\Mailcoach\Livewire\Mails\SuppressionListComponent;
use Spatie\Mailcoach\Livewire\SendTestComponent;
use Spatie\Mailcoach\Livewire\Spotlight\AutomationEmailsCommand;
use Spatie\Mailcoach\Livewire\Spotlight\AutomationsCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CampaignsCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CreateAutomationCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CreateAutomationMailCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CreateCampaignCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CreateListCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CreateTemplateCommand;
use Spatie\Mailcoach\Livewire\Spotlight\CreateTransactionalTemplateCommand;
use Spatie\Mailcoach\Livewire\Spotlight\HomeCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ListsCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ShowAutomationCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ShowAutomationMailCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ShowCampaignCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ShowListCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ShowTemplateCommand;
use Spatie\Mailcoach\Livewire\Spotlight\ShowTransactionalTemplateCommand;
use Spatie\Mailcoach\Livewire\Spotlight\TemplatesCommand;
use Spatie\Mailcoach\Livewire\Spotlight\TransactionalLogCommand;
use Spatie\Mailcoach\Livewire\Spotlight\TransactionalTemplatesCommand;
use Spatie\Mailcoach\Livewire\Templates\CreateTemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplateComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\CreateTransactionalTemplateComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailContentComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailLogItemsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailPerformanceComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailResendComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateContentComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalTemplateSettingsComponent;
use Spatie\Mailcoach\Livewire\Webhooks\CreateWebhookComponent;
use Spatie\Mailcoach\Livewire\Webhooks\EditWebhookComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhookLogComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhookLogsComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent;
use Spatie\Navigation\Helpers\ActiveUrlChecker;

class MailcoachServiceProvider extends PackageServiceProvider
{
    use UsesMailcoachModels;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mailcoach')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations([
                'create_mailcoach_tables',
                'create_media_table',
                'create_webhook_calls_table',
            ])
            ->hasCommands([
                CalculateStatisticsCommand::class,
                CalculateAutomationMailStatisticsCommand::class,
                SendAutomationMailsCommand::class,
                SendScheduledCampaignsCommand::class,
                SendCampaignMailsCommand::class,
                SendCampaignSummaryMailCommand::class,
                SendEmailListSummaryMailCommand::class,
                RetryPendingSendsCommand::class,
                DeleteOldUnconfirmedSubscribersCommand::class,
                CleanupProcessedFeedbackCommand::class,
                RunAutomationActionsCommand::class,
                RunAutomationTriggersCommand::class,
                CheckLicenseCommand::class,
                DeleteOldExportsCommand::class,
                PublishCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Version::class, fn () => new Version());

        $this->app->scoped(MainNavigation::class, function () {
            return new MainNavigation(app(ActiveUrlChecker::class));
        });

        $this->app->scoped(SettingsNavigation::class, function () {
            return new SettingsNavigation(app(ActiveUrlChecker::class));
        });

        $this->app->scoped(SimpleThrottle::class, function () {
            $cache = cache()->store(config('mailcoach.throttling.cache_store'));

            $simpleThrottleCache = new SimpleThrottleCache($cache);

            return SimpleThrottle::create($simpleThrottleCache);
        });
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
                $this->package->basePath('/../resources/images') => public_path("vendor/{$this->package->shortName()}/images"),
            ], "{$this->package->shortName()}-assets");
        }

        $this
            ->bootCarbon()
            ->bootConfig()
            ->bootGate()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootViews()
            ->bootEvents()
            ->bootTriggers()
            ->bootSpotlight();
    }

    protected function bootCarbon(): static
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Date::macro(
            'toMailcoachFormat',
            /** @phpstan-ignore-next-line */
            fn () => self::this()->copy()->setTimezone(config('mailcoach.timezone') ?? config('app.timezone'))->format($mailcoachFormat)
        );

        return $this;
    }

    protected function bootConfig(): static
    {
        if (! config('mailcoach.boot_config', true)) {
            return $this;
        }

        try {
            self::getMailerClass()::registerAllConfigValues();
        } catch (QueryException) {
            // Do nothing as table probably doesn't exist
        }

        app(AppConfiguration::class)->registerConfigValues();
        app(EditorConfiguration::class)->registerConfigValues();

        return $this;
    }

    protected function bootSupportMacros(): static
    {
        if (! Str::hasMacro('shortNumber')) {
            Str::macro('shortNumber', function (int $number, int $decimals = 1) {
                if ($number < 1_000) {
                    $format = number_format($number, $decimals);
                    $suffix = '';
                } elseif ($number < 1_000_000) {
                    $format = number_format(floor($number / 100) / 10, $decimals);
                    $suffix = 'K';
                } elseif ($number < 1_000_000_000) {
                    $format = number_format(floor($number / 100000) / 10, $decimals);
                    $suffix = 'M';
                } else {
                    return '🤯';
                }

                if ($decimals > 0) {
                    $dotzero = '.'.str_repeat('0', $decimals);
                    $format = str_replace($dotzero, '', $format);
                }

                return $format.$suffix;
            });
        }

        return $this;
    }

    protected function bootGate(): static
    {
        Gate::define('viewMailcoach', fn (MailcoachUser $user) => $user->canViewMailcoach());

        return $this;
    }

    protected function bootRoutes(): static
    {
        Route::macro('mailcoach', function (
            string $url = '',
            bool $registerFeedback = true,
        ) {
            Route::middleware([BootstrapMailcoach::class])->group(function () use ($url, $registerFeedback) {
                if ($registerFeedback) {
                    Route::namespace(null)->group(function () {
                        Route::sesFeedback('ses-feedback');
                        Route::mailgunFeedback('mailgun-feedback');
                        Route::sendgridFeedback('sendgrid-feedback');
                        Route::postmarkFeedback('postmark-feedback');
                        Route::sendinblueFeedback('sendinblue-feedback');
                    });
                }

                Route::prefix($url)->namespace(null)->group(function () {
                    Route::prefix('')
                        ->group(__DIR__.'/../routes/mailcoach-public-api.php');

                    Route::prefix('')
                        ->middleware(config('mailcoach.middleware')['web'])
                        ->group(__DIR__.'/../routes/mailcoach-ui.php');

                    Route::prefix('api')
                        ->middleware(config('mailcoach.middleware')['api'])
                        ->group(__DIR__.'/../routes/mailcoach-api.php');

                    /*
                     * The website routes should be registered last, so that
                     * they don't eat up other routes
                     */
                    Route::prefix(config('mailcoach.website_prefix', 'archive'))
                        ->middleware('web')
                        ->group(__DIR__.'/../routes/mailcoach-email-list-website.php');
                });

                Route::mailcoachEditor('mailcoachEditor');
                Route::get($url, function () {
                    return redirect()->route(config('mailcoach.redirect_home', 'mailcoach.dashboard'));
                })->name('mailcoach.home');
            });
        });

        return $this;
    }

    protected function bootViews(): static
    {
        View::composer('mailcoach::emailListWebsite.partials.style', WebsiteStyleComposer::class);

        $this->bootBladeComponents();
        $this->bootLivewireComponents();

        return $this;
    }

    protected function bootBladeComponents(): static
    {
        Blade::component('mailcoach::app.components.form.checkboxField', 'mailcoach::checkbox-field');
        Blade::component('mailcoach::app.components.form.radioField', 'mailcoach::radio-field');
        Blade::component('mailcoach::app.components.form.formButton', 'mailcoach::form-button');
        Blade::component('mailcoach::app.components.form.formButtons', 'mailcoach::form-buttons');
        Blade::component('mailcoach::app.components.form.confirmButton', 'mailcoach::confirm-button');
        Blade::component('mailcoach::app.components.form.selectField', 'mailcoach::select-field');
        Blade::component('mailcoach::app.components.form.comboBoxField', 'mailcoach::combo-box-field');
        Blade::component('mailcoach::app.components.form.tagsField', 'mailcoach::tags-field');
        Blade::component('mailcoach::app.components.form.textField', 'mailcoach::text-field');
        Blade::component('mailcoach::app.components.form.htmlField', 'mailcoach::html-field');
        Blade::component('mailcoach::app.components.form.markdownField', 'mailcoach::markdown-field');
        Blade::component('mailcoach::app.components.form.colorField', 'mailcoach::color-field');
        Blade::component('mailcoach::app.components.form.textArea', 'mailcoach::textarea-field');

        Blade::component('mailcoach::app.components.form.templateChooser', 'mailcoach::template-chooser');

        Blade::component('mailcoach::app.components.form.dateField', 'mailcoach::date-field');
        Blade::component('mailcoach::app.components.form.fieldset', 'mailcoach::fieldset');
        Blade::component(DateTimeFieldComponent::class, 'mailcoach::date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'mailcoach::modal');
        Blade::component('mailcoach::app.components.modal.previewModal', 'mailcoach::preview-modal');

        Blade::component('mailcoach::app.components.card', 'mailcoach::card');
        Blade::component('mailcoach::app.components.tile', 'mailcoach::tile');

        Blade::component('mailcoach::app.components.dataTable', 'mailcoach::data-table');
        Blade::component('mailcoach::app.components.table.tableStatus', 'mailcoach::table-status');
        Blade::component('mailcoach::app.components.table.th', 'mailcoach::th');

        Blade::component('mailcoach::app.components.filters.filters', 'mailcoach::filters');
        Blade::component('mailcoach::app.components.filters.filter', 'mailcoach::filter');

        Blade::component('mailcoach::app.components.search', 'mailcoach::search');
        Blade::component('mailcoach::app.components.statistic', 'mailcoach::statistic');
        Blade::component('mailcoach::app.components.iconLabel', 'mailcoach::icon-label');
        Blade::component('mailcoach::app.components.healthLabel', 'mailcoach::health-label');
        Blade::component('mailcoach::app.components.roundedIcon', 'mailcoach::rounded-icon');

        Blade::component('mailcoach::app.components.navigation.main', 'mailcoach::main-navigation');
        Blade::component('mailcoach::app.components.navigation.root', 'mailcoach::navigation');
        Blade::component('mailcoach::app.components.navigation.item', 'mailcoach::navigation-item');
        Blade::component('mailcoach::app.components.navigation.group', 'mailcoach::navigation-group');
        Blade::component('mailcoach::app.components.navigation.tabs', 'mailcoach::navigation-tabs');

        Blade::component('mailcoach::app.components.alert.info', 'mailcoach::info');
        Blade::component('mailcoach::app.components.alert.help', 'mailcoach::help');
        Blade::component('mailcoach::app.components.alert.warning', 'mailcoach::warning');
        Blade::component('mailcoach::app.components.alert.error', 'mailcoach::error');
        Blade::component('mailcoach::app.components.alert.success', 'mailcoach::success');

        Blade::component('mailcoach::app.components.counter', 'mailcoach::counter');
        Blade::component('mailcoach::app.components.addressDefinition', 'mailcoach::address-definition');
        Blade::component('mailcoach::app.components.webview', 'mailcoach::web-view');

        Blade::component('mailcoach::app.components.button.primary', 'mailcoach::button');
        Blade::component('mailcoach::app.components.button.secondary', 'mailcoach::button-secondary');
        Blade::component('mailcoach::app.components.button.cancel', 'mailcoach::button-cancel');

        Blade::component('mailcoach::app.components.editorButtons', 'mailcoach::editor-buttons');
        Blade::component('mailcoach::app.components.editorFields', 'mailcoach::editor-fields');
        Blade::component(ReplacerHelpTextsComponent::class, 'mailcoach::replacer-help-texts');

        Blade::component('mailcoach::app.components.dropdown', 'mailcoach::dropdown');

        Blade::component('mailcoach::app.layouts.app', 'mailcoach::layout');
        Blade::component('mailcoach::app.automations.layouts.automation', 'mailcoach::layout-automation');
        Blade::component('mailcoach::app.campaigns.layouts.campaign', 'mailcoach::layout-campaign');
        Blade::component('mailcoach::app.emailLists.layouts.emailList', 'mailcoach::layout-list');
        Blade::component('mailcoach::app.emailLists.segments.layouts.segment', 'mailcoach::layout-segment');
        Blade::component('mailcoach::app.emailLists.subscribers.layouts.subscriber', 'mailcoach::layout-subscriber');
        Blade::component('mailcoach::app.transactionalMails.layouts.transactional', 'mailcoach::layout-transactional');
        Blade::component('mailcoach::app.transactionalMails.templates.layouts.template', 'mailcoach::layout-transactional-template');
        Blade::component('mailcoach::app.automations.mails.layouts.automationMail', 'mailcoach::layout-automation-mail');

        Blade::component('mailcoach::app.automations.components.automationAction', 'mailcoach::automation-action');
        Blade::component('mailcoach::app.conditionBuilder.conditions.condition', 'mailcoach::condition');

        Blade::component('mailcoach::app.layouts.settings', 'mailcoach::layout-settings');

        Blade::component('mailcoach::app.components.codeCopy', 'mailcoach::code-copy');

        Blade::component('mailcoach::emailListWebsite.layouts.emailListWebsite', 'mailcoach::layout-website');

        return $this;
    }

    protected function bootLivewireComponents(): static
    {
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/mailcoach/livewire/update', $handle)
                ->middleware(config('mailcoach.middleware.web'));
        });

        Livewire::component('mailcoach::email-list-count', Mailcoach::getLivewireClass(EmailListCountComponent::class));
        Livewire::component('mailcoach::segment-population-count', Mailcoach::getLivewireClass(SegmentPopulationCountComponent::class));
        Livewire::component('mailcoach::tag-population-count', Mailcoach::getLivewireClass(TagPopulationCountComponent::class));
        Livewire::component('mailcoach::text-area-editor', Mailcoach::getLivewireClass(TextAreaEditorComponent::class));
        Livewire::component('mailcoach::link-check', Mailcoach::getLivewireClass(LinkCheckComponent::class));

        Livewire::component('mailcoach::automation-builder', Mailcoach::getLivewireClass(AutomationBuilder::class));

        Livewire::component('mailcoach::automation-action', Mailcoach::getLivewireClass(AutomationActionComponent::class));
        Livewire::component('mailcoach::automation-mail-action', Mailcoach::getLivewireClass(AutomationMailActionComponent::class));
        Livewire::component('mailcoach::add-tags-action', Mailcoach::getLivewireClass(AddTagsActionComponent::class));
        Livewire::component('mailcoach::remove-tags-action', Mailcoach::getLivewireClass(RemoveTagsActionComponent::class));
        Livewire::component('mailcoach::wait-action', Mailcoach::getLivewireClass(WaitActionComponent::class));
        Livewire::component('mailcoach::condition-action', Mailcoach::getLivewireClass(ConditionActionComponent::class));
        Livewire::component('mailcoach::split-action', Mailcoach::getLivewireClass(SplitActionComponent::class));
        Livewire::component('mailcoach::send-webhook-action', Mailcoach::getLivewireClass(SendWebhookActionComponent::class));
        Livewire::component('mailcoach::email-list-action', Mailcoach::getLivewireClass(SubscribeToEmailListActionComponent::class));

        Livewire::component('mailcoach::date-trigger', Mailcoach::getLivewireClass(DateTriggerComponent::class));
        Livewire::component('mailcoach::tag-added-trigger', Mailcoach::getLivewireClass(TagAddedTriggerComponent::class));
        Livewire::component('mailcoach::tag-removed-trigger', Mailcoach::getLivewireClass(TagRemovedTriggerComponent::class));
        Livewire::component('mailcoach::webhook-trigger', Mailcoach::getLivewireClass(WebhookTriggerComponent::class));
        Livewire::component('mailcoach::no-trigger', Mailcoach::getLivewireClass(NoTriggerComponent::class));

        Livewire::component('mailcoach::email-list-statistics', Mailcoach::getLivewireClass(EmailListStatistics::class));
        Livewire::component('mailcoach::campaign-statistics', Mailcoach::getLivewireClass(CampaignStatisticsComponent::class));

        Livewire::component('mailcoach::send-test', Mailcoach::getLivewireClass(SendTestComponent::class));

        Livewire::component('mailcoach::dashboard', Mailcoach::getLivewireClass(DashboardComponent::class));
        Livewire::component('mailcoach::dashboard-chart', Mailcoach::getLivewireClass(DashboardChart::class));

        // Audience
        Livewire::component('mailcoach::create-list', Mailcoach::getLivewireClass(CreateListComponent::class));
        Livewire::component('mailcoach::lists', Mailcoach::getLivewireClass(ListsComponent::class));
        Livewire::component('mailcoach::list-summary', Mailcoach::getLivewireClass(ListSummaryComponent::class));
        Livewire::component('mailcoach::list-settings', Mailcoach::getLivewireClass(ListSettingsComponent::class));
        Livewire::component('mailcoach::list-onboarding', Mailcoach::getLivewireClass(ListOnboardingComponent::class));
        Livewire::component('mailcoach::list-mailers', Mailcoach::getLivewireClass(ListMailersComponent::class));
        Livewire::component('mailcoach::website', Mailcoach::getLivewireClass(WebsiteComponent::class));
        Livewire::component('mailcoach::create-segment', Mailcoach::getLivewireClass(CreateSegmentComponent::class));
        Livewire::component('mailcoach::segments', Mailcoach::getLivewireClass(SegmentsComponent::class));
        Livewire::component('mailcoach::segment', Mailcoach::getLivewireClass(SegmentComponent::class));
        Livewire::component('mailcoach::segment-subscribers', Mailcoach::getLivewireClass(SegmentSubscribersComponent::class));
        Livewire::component('mailcoach::create-subscriber', Mailcoach::getLivewireClass(CreateSubscriberComponent::class));
        Livewire::component('mailcoach::subscribers', Mailcoach::getLivewireClass(SubscribersComponent::class));
        Livewire::component('mailcoach::subscriber', Mailcoach::getLivewireClass(SubscriberComponent::class));
        Livewire::component('mailcoach::subscriber-sends', Mailcoach::getLivewireClass(SubscriberSendsComponent::class));
        Livewire::component('mailcoach::subscriber-imports', Mailcoach::getLivewireClass(SubscriberImportsComponent::class));
        Livewire::component('mailcoach::subscriber-exports', Mailcoach::getLivewireClass(SubscriberExportsComponent::class));
        Livewire::component('mailcoach::create-tag', Mailcoach::getLivewireClass(CreateTagComponent::class));
        Livewire::component('mailcoach::tags', Mailcoach::getLivewireClass(TagsComponent::class));
        Livewire::component('mailcoach::tag', Mailcoach::getLivewireClass(TagComponent::class));

        // Automations
        Livewire::component('mailcoach::create-automation', Mailcoach::getLivewireClass(CreateAutomationComponent::class));
        Livewire::component('mailcoach::automations', Mailcoach::getLivewireClass(AutomationsComponent::class));
        Livewire::component('mailcoach::automation-settings', Mailcoach::getLivewireClass(AutomationSettingsComponent::class));
        Livewire::component('mailcoach::automation-actions', Mailcoach::getLivewireClass(AutomationActionsComponent::class));
        Livewire::component('mailcoach::automation-run', Mailcoach::getLivewireClass(RunAutomationComponent::class));
        Livewire::component('mailcoach::create-automation-mail', Mailcoach::getLivewireClass(CreateAutomationMailComponent::class));
        Livewire::component('mailcoach::automation-mails', Mailcoach::getLivewireClass(AutomationMailsComponent::class));
        Livewire::component('mailcoach::automation-mail-summary', Mailcoach::getLivewireClass(AutomationMailSummaryComponent::class));
        Livewire::component('mailcoach::automation-mail-settings', Mailcoach::getLivewireClass(AutomationMailSettingsComponent::class));
        Livewire::component('mailcoach::automation-mail-content', Mailcoach::getLivewireClass(AutomationMailContentComponent::class));
        Livewire::component('mailcoach::automation-mail-clicks', Mailcoach::getLivewireClass(AutomationMailClicksComponent::class));
        Livewire::component('mailcoach::automation-mail-opens', Mailcoach::getLivewireClass(AutomationMailOpensComponent::class));
        Livewire::component('mailcoach::automation-mail-unsubscribes', Mailcoach::getLivewireClass(AutomationMailUnsubscribesComponent::class));
        Livewire::component('mailcoach::automation-mail-outbox', Mailcoach::getLivewireClass(AutomationMailOutboxComponent::class));

        // Campaigns
        Livewire::component('mailcoach::create-campaign', Mailcoach::getLivewireClass(CreateCampaignComponent::class));
        Livewire::component('mailcoach::campaigns', Mailcoach::getLivewireClass(CampaignsComponent::class));
        Livewire::component('mailcoach::create-template', Mailcoach::getLivewireClass(CreateTemplateComponent::class));
        Livewire::component('mailcoach::templates', Mailcoach::getLivewireClass(TemplatesComponent::class));
        Livewire::component('mailcoach::template', Mailcoach::getLivewireClass(TemplateComponent::class));
        Livewire::component('mailcoach::campaign-content', Mailcoach::getLivewireClass(CampaignContentComponent::class));
        Livewire::component('mailcoach::campaign-settings', Mailcoach::getLivewireClass(CampaignSettingsComponent::class));
        Livewire::component('mailcoach::campaign-delivery', Mailcoach::getLivewireClass(CampaignDeliveryComponent::class));
        Livewire::component('mailcoach::campaign-summary', Mailcoach::getLivewireClass(CampaignSummaryComponent::class));
        Livewire::component('mailcoach::campaign-clicks', Mailcoach::getLivewireClass(CampaignClicksComponent::class));
        Livewire::component('mailcoach::campaign-link-clicks', Mailcoach::getLivewireClass(CampaignLinkClicksComponent::class));
        Livewire::component('mailcoach::campaign-opens', Mailcoach::getLivewireClass(CampaignOpensComponent::class));
        Livewire::component('mailcoach::campaign-unsubscribes', Mailcoach::getLivewireClass(CampaignUnsubscribesComponent::class));
        Livewire::component('mailcoach::campaign-outbox', Mailcoach::getLivewireClass(CampaignOutboxComponent::class));

        // TransactionalMails
        Livewire::component('mailcoach::create-transactional-template', Mailcoach::getLivewireClass(CreateTransactionalTemplateComponent::class));
        Livewire::component('mailcoach::transactional-mails', Mailcoach::getLivewireClass(TransactionalMailLogItemsComponent::class));
        Livewire::component('mailcoach::transactional-mail-templates', Mailcoach::getLivewireClass(TransactionalMailsComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-content', Mailcoach::getLivewireClass(TransactionalTemplateContentComponent::class));
        Livewire::component('mailcoach::transactional-mail-template-settings', Mailcoach::getLivewireClass(TransactionalTemplateSettingsComponent::class));
        Livewire::component('mailcoach::transactional-mail-content', Mailcoach::getLivewireClass(TransactionalMailContentComponent::class));
        Livewire::component('mailcoach::transactional-mail-performance', Mailcoach::getLivewireClass(TransactionalMailPerformanceComponent::class));
        Livewire::component('mailcoach::transactional-mail-resend', Mailcoach::getLivewireClass(TransactionalMailResendComponent::class));

        Livewire::component('mailcoach::export', Mailcoach::getLivewireClass(ExportComponent::class));
        Livewire::component('mailcoach::import', Mailcoach::getLivewireClass(ImportComponent::class));

        // settings
        Livewire::component('mailcoach::webhooks', Mailcoach::getLivewireClass(WebhooksComponent::class));
        Livewire::component('mailcoach::create-webhook', Mailcoach::getLivewireClass(CreateWebhookComponent::class));
        Livewire::component('mailcoach::edit-webhook', Mailcoach::getLivewireClass(EditWebhookComponent::class));
        Livewire::component('mailcoach::webhook-logs', Mailcoach::getLivewireClass(WebhookLogsComponent::class));
        Livewire::component('mailcoach::webhook-log', Mailcoach::getLivewireClass(WebhookLogComponent::class));

        Livewire::component('mailcoach::mailers', Mailcoach::getLivewireClass(MailersComponent::class));
        Livewire::component('mailcoach::create-mailer', Mailcoach::getLivewireClass(CreateMailerComponent::class));
        Livewire::component('mailcoach::general-settings', Mailcoach::getLivewireClass(GeneralSettingsComponent::class));
        Livewire::component('mailcoach::editor-settings', Mailcoach::getLivewireClass(EditorSettingsComponent::class));
        Livewire::component('mailcoach::mailer-send-test', Mailcoach::getLivewireClass(\Spatie\Mailcoach\Livewire\MailConfiguration\SendTestComponent::class));
        Livewire::component('mailcoach::edit-mailer', Mailcoach::getLivewireClass(EditMailerComponent::class));
        Livewire::component('mailcoach::suppression-list', Mailcoach::getLivewireClass(SuppressionListComponent::class));
        Livewire::component('mailcoach::create-suppression', Mailcoach::getLivewireClass(CreateSuppressionComponent::class));

        // Condition builder
        Livewire::component('mailcoach::condition-builder', Mailcoach::getLivewireClass(ConditionBuilderComponent::class));
        Livewire::component('mailcoach::subscriber-tags-condition', Mailcoach::getLivewireClass(SubscriberTagsConditionComponent::class));
        Livewire::component('mailcoach::subscriber-subscribed-at-condition', Mailcoach::getLivewireClass(SubscriberSubscribedAtConditionComponent::class));
        Livewire::component('mailcoach::subscriber-attributes-condition', Mailcoach::getLivewireClass(SubscriberAttributesConditionComponent::class));
        Livewire::component('mailcoach::subscriber-opened-campaign-condition', Mailcoach::getLivewireClass(SubscriberOpenedCampaignConditionComponent::class));
        Livewire::component('mailcoach::subscriber-opened-automation-mail-condition', Mailcoach::getLivewireClass(SubscriberOpenedAutomationMailConditionComponent::class));
        Livewire::component('mailcoach::subscriber-clicked-automation-mail-link-condition', Mailcoach::getLivewireClass(SubscriberClickedAutomationMailLinkConditionComponent::class));
        Livewire::component('mailcoach::subscriber-clicked-campaign-link-condition', Mailcoach::getLivewireClass(SubscriberClickedCampaignLinkConditionComponent::class));
        Livewire::component('mailcoach::subscriber-email-condition', Mailcoach::getLivewireClass(SubscriberEmailQueryConditionComponent::class));

        SesSetupWizardComponent::registerLivewireComponents();
        SendGridSetupWizardComponent::registerLivewireComponents();
        SendinblueSetupWizardComponent::registerLivewireComponents();
        SmtpSetupWizardComponent::registerLivewireComponents();
        PostmarkSetupWizardComponent::registerLivewireComponents();
        MailgunSetupWizardComponent::registerLivewireComponents();

        return $this;
    }

    protected function bootEvents(): static
    {
        Event::listen(CampaignSentEvent::class, SendCampaignSentEmail::class);
        Event::listen(WebhookCallProcessedEvent::class, SetWebhookCallProcessedAt::class);
        Event::listen(MessageSending::class, StoreTransactionalMail::class);
        Event::listen(CampaignOpenedEvent::class, AddCampaignOpenedTag::class);
        Event::listen(CampaignLinkClickedEvent::class, AddCampaignClickedTag::class);
        Event::listen(AutomationMailOpenedEvent::class, AddAutomationMailOpenedTag::class);
        Event::listen(AutomationMailLinkClickedEvent::class, AddAutomationMailClickedTag::class);

        Event::subscribe(WebhookEventSubscriber::class);
        Event::subscribe(WebhookLogEventSubscriber::class);
        Event::subscribe(WebhookFailedAttemptsSubscriber::class);

        return $this;
    }

    protected function bootTriggers(): static
    {
        if (! config('mailcoach.boot_triggers', true)) {
            return $this;
        }

        try {
            $triggers = cache()->rememberForever('automation-triggers', function () {
                return static::getAutomationTriggerClass()::with(['automation'])->has('automation')->get();
            });

            $triggers
                ->filter(fn (Trigger $trigger) => $trigger->trigger instanceof TriggeredByEvents)
                ->each(function (Trigger $trigger) {
                    if ($trigger->automation) {
                        Event::subscribe($trigger->trigger->setAutomation($trigger->automation));
                    }
                });
        } catch (Exception) {
            // Do nothing as the database is probably not set up yet.
        }

        return $this;
    }

    protected function bootSpotlight(): static
    {
        return $this;

        // Index commands
        Spotlight::registerCommand(AutomationsCommand::class);
        Spotlight::registerCommand(AutomationEmailsCommand::class);
        Spotlight::registerCommand(CampaignsCommand::class);
        Spotlight::registerCommand(HomeCommand::class);
        Spotlight::registerCommand(ListsCommand::class);
        Spotlight::registerCommand(TemplatesCommand::class);
        Spotlight::registerCommand(TransactionalLogCommand::class);
        Spotlight::registerCommand(TransactionalTemplatesCommand::class);

        // Show commands
        Spotlight::registerCommand(ShowAutomationCommand::class);
        Spotlight::registerCommand(ShowAutomationMailCommand::class);
        Spotlight::registerCommand(ShowCampaignCommand::class);
        Spotlight::registerCommand(ShowListCommand::class);
        Spotlight::registerCommand(ShowTemplateCommand::class);
        Spotlight::registerCommand(ShowTransactionalTemplateCommand::class);

        // Create commands
        Spotlight::registerCommand(CreateAutomationCommand::class);
        Spotlight::registerCommand(CreateAutomationMailCommand::class);
        Spotlight::registerCommand(CreateCampaignCommand::class);
        Spotlight::registerCommand(CreateListCommand::class);
        Spotlight::registerCommand(CreateTemplateCommand::class);
        Spotlight::registerCommand(CreateTransactionalTemplateCommand::class);

        config()->set('livewire-ui-spotlight.show_results_without_input', true);
        config()->set('livewire-ui-spotlight.shortcuts', ['slash']);
        config()->set('livewire-ui-spotlight.include_js', false);
        config()->set('livewire-ui-spotlight.include_css', false);

        return $this;
    }
}
