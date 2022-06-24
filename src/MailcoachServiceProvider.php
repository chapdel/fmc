<?php

namespace Spatie\Mailcoach;

use Exception;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
use Spatie\Mailcoach\Components\AutomationMailReplacerHelpTextsComponent;
use Spatie\Mailcoach\Components\CampaignReplacerHelpTextsComponent;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\TransactionalMailTemplateReplacerHelpTextsComponent;
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
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\SplitActionComponent;
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
use Spatie\Mailcoach\Domain\Shared\Commands\CheckLicenseCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\CleanupProcessedFeedbackCommand;
use Spatie\Mailcoach\Domain\Shared\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottleCache;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Http\App\Controllers\HomeController;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateList;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSegment;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSubscriber;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateTag;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListMailers;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListOnboarding;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Lists;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListSettings;
use Spatie\Mailcoach\Http\App\Livewire\Audience\ListSummary;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Segment;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Segments;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentSubscribers;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Subscriber;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Subscribers;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SubscriberSends;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Tag;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Tags;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationActions;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailClicks;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOpens;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailOutbox;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMails;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSettings;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailSummary;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationMailUnsubscribes;
use Spatie\Mailcoach\Http\App\Livewire\Automations\Automations;
use Spatie\Mailcoach\Http\App\Livewire\Automations\AutomationSettings;
use Spatie\Mailcoach\Http\App\Livewire\Automations\CreateAutomation;
use Spatie\Mailcoach\Http\App\Livewire\Automations\CreateAutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\Automations\RunAutomation;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignClicks;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignDelivery;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOpens;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignOutbox;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettings;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignStatistics;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSummary;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignUnsubscribes;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateCampaign;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateTemplate;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Template;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Templates;
use Spatie\Mailcoach\Http\App\Livewire\Dashboard;
use Spatie\Mailcoach\Http\App\Livewire\DashboardChart;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Livewire\Export\Export;
use Spatie\Mailcoach\Http\App\Livewire\Import\Import;
use Spatie\Mailcoach\Http\App\Livewire\SendTest;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\AutomationEmailsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\AutomationsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CampaignsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateAutomationCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateAutomationMailCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateCampaignCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateListCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\CreateTransactionalTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\HomeCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ListsCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowAutomationCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowAutomationMailCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowCampaignCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowListCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\ShowTransactionalTemplateCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\TemplatesCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\TransactionalLogCommand;
use Spatie\Mailcoach\Http\App\Livewire\Spotlight\TransactionalTemplatesCommand;
use Spatie\Mailcoach\Http\App\Livewire\TextAreaEditorComponent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\CreateTransactionalTemplate;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailContent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailPerformance;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMailResend;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalMails;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateContent;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplates;
use Spatie\Mailcoach\Http\App\Livewire\TransactionalMails\TransactionalTemplateSettings;
use Spatie\Mailcoach\Http\App\ViewComposers\FooterComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\IndexComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\QueryStringComposer;
use Spatie\Navigation\Helpers\ActiveUrlChecker;
use Spatie\QueryString\QueryString;

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
            ->hasAssets()
            ->hasMigrations([
                '2022_02_10_000001_create_mailcoach_tables',
                '2022_02_10_000002_create_media_table',
                '2022_02_10_000003_create_webhook_calls_table',
            ])
            ->hasCommands([
                CalculateStatisticsCommand::class,
                CalculateAutomationMailStatisticsCommand::class,
                SendAutomationMailsCommand::class,
                SendScheduledCampaignsCommand::class,
                SendCampaignSummaryMailCommand::class,
                SendEmailListSummaryMailCommand::class,
                RetryPendingSendsCommand::class,
                DeleteOldUnconfirmedSubscribersCommand::class,
                CleanupProcessedFeedbackCommand::class,
                RunAutomationActionsCommand::class,
                RunAutomationTriggersCommand::class,
                CheckLicenseCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(QueryString::class, fn () => new QueryString(urldecode(request()->getRequestUri())));

        $this->app->singleton(Version::class, function () {
            return new Version();
        });

        $this->app->scoped(MainNavigation::class, function () {
            return new MainNavigation(app(ActiveUrlChecker::class));
        });

        $this->app->scoped(SimpleThrottle::class, function () {
            $cache = cache()->store(config('mailcoach.shared.throttling.cache_store'));

            $simpleThrottleCache = new SimpleThrottleCache($cache);

            return SimpleThrottle::create($simpleThrottleCache);
        });
    }

    public function packageBooted(): void
    {
        $this
            ->bootCarbon()
            ->bootGate()
            ->bootMigrations()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootTranslations()
            ->bootViews()
            ->bootEvents()
            ->bootTriggers()
            ->registerDeprecatedApiGuard()
            ->bootSpotlight();
    }

    protected function bootCarbon(): self
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Date::macro(
            'toMailcoachFormat',
            /** @phpstan-ignore-next-line */
            fn () => self::this()->copy()->setTimezone(config('app.timezone'))->format($mailcoachFormat)
        );

        return $this;
    }

    protected function bootSupportMacros(): self
    {
        if (! Collection::hasMacro('paginate')) {
            Collection::macro('paginate', function (int $perPage = 15, string $pageName = 'page', int $page = null, int $total = null, array $options = []): LengthAwarePaginator {
                $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

                /** @var Collection $this */
                $results = $this->forPage($page, $perPage)->values();

                $total = $total ?: $this->count();

                $options += [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ];

                return new LengthAwarePaginator($results, $total, $perPage, $page, $options);
            });
        }

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
                    return "🤯";
                }

                if ($decimals > 0) {
                    $dotzero = '.' . str_repeat('0', $decimals);
                    $format = str_replace($dotzero, '', $format);
                }

                return $format . $suffix;
            });
        }

        return $this;
    }

    protected function bootGate(): self
    {
        Gate::define('viewMailcoach', fn () => $this->app->environment('local'));

        return $this;
    }

    protected function bootMigrations(): self
    {
        if (Mailcoach::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        return $this;
    }

    protected function bootRoutes(): self
    {
        Route::model('transactionalMailTemplate', self::getTransactionalMailTemplateClass());

        Route::macro('mailcoach', function (string $url = '') {
            Route::get($url, '\\' . HomeController::class)->name('mailcoach.home');

            Route::prefix($url)->group(function () {
                Route::prefix('')->group(__DIR__ . '/../routes/mailcoach-public-api.php');

                Route::prefix('')
                    ->middleware(config('mailcoach.middleware')['web'])
                    ->group(__DIR__ . '/../routes/mailcoach-ui.php');

                Route::prefix('api')
                    ->middleware(config('mailcoach.middleware')['api'])
                    ->group(__DIR__ . '/../routes/mailcoach-api.php');
            });
        });

        return $this;
    }

    protected function bootViews(): self
    {
        View::composer('mailcoach::*', QueryStringComposer::class);
        View::composer('mailcoach::*.index', IndexComposer::class);

        View::composer('mailcoach::app.layouts.partials.footer', FooterComposer::class);

        if (config("mailcoach.views.use_blade_components", true)) {
            $this->bootBladeComponents();
        }

        $this->bootLivewireComponents();

        return $this;
    }

    protected function bootTranslations(): self
    {
        $this->loadJSONTranslationsFrom(__DIR__ . '/../resources/lang/');

        return $this;
    }

    protected function bootBladeComponents(): self
    {
        Blade::component('mailcoach::app.components.form.checkboxField', 'mailcoach::checkbox-field');
        Blade::component('mailcoach::app.components.form.radioField', 'mailcoach::radio-field');
        Blade::component('mailcoach::app.components.form.formButton', 'mailcoach::form-button');
        Blade::component('mailcoach::app.components.form.confirmButton', 'mailcoach::confirm-button');
        Blade::component('mailcoach::app.components.form.selectField', 'mailcoach::select-field');
        Blade::component('mailcoach::app.components.form.tagsField', 'mailcoach::tags-field');
        Blade::component('mailcoach::app.components.form.textField', 'mailcoach::text-field');
        Blade::component('mailcoach::app.components.form.htmlField', 'mailcoach::html-field');
        Blade::component('mailcoach::app.components.form.templateChooser', 'mailcoach::template-chooser');

        Blade::component('mailcoach::app.components.form.dateField', 'mailcoach::date-field');
        Blade::component('mailcoach::app.components.form.fieldset', 'mailcoach::fieldset');
        Blade::component(DateTimeFieldComponent::class, 'mailcoach::date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'mailcoach::modal');
        Blade::component('mailcoach::app.components.modal.previewModal', 'mailcoach::preview-modal');

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
        Blade::component(CampaignReplacerHelpTextsComponent::class, 'mailcoach::campaign-replacer-help-texts');

        Blade::component(AutomationMailReplacerHelpTextsComponent::class, 'mailcoach::automation-mail-replacer-help-texts');

        Blade::component(TransactionalMailTemplateReplacerHelpTextsComponent::class, 'mailcoach::transactional-mail-template-replacer-help-texts');

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

        return $this;
    }

    protected function bootLivewireComponents(): self
    {
        Livewire::component('mailcoach::text-area-editor', TextAreaEditorComponent::class);

        Livewire::component('mailcoach::automation-builder', AutomationBuilder::class);

        Livewire::component('mailcoach::automation-action', AutomationActionComponent::class);
        Livewire::component('mailcoach::automation-mail-action', AutomationMailActionComponent::class);
        Livewire::component('mailcoach::add-tags-action', AddTagsActionComponent::class);
        Livewire::component('mailcoach::remove-tags-action', RemoveTagsActionComponent::class);
        Livewire::component('mailcoach::wait-action', WaitActionComponent::class);
        Livewire::component('mailcoach::condition-action', ConditionActionComponent::class);
        Livewire::component('mailcoach::split-action', SplitActionComponent::class);

        Livewire::component('mailcoach::date-trigger', DateTriggerComponent::class);
        Livewire::component('mailcoach::tag-added-trigger', TagAddedTriggerComponent::class);
        Livewire::component('mailcoach::tag-removed-trigger', TagRemovedTriggerComponent::class);
        Livewire::component('mailcoach::webhook-trigger', WebhookTriggerComponent::class);
        Livewire::component('mailcoach::no-trigger', NoTriggerComponent::class);

        Livewire::component('mailcoach::email-list-statistics', EmailListStatistics::class);
        Livewire::component('mailcoach::campaign-statistics', CampaignStatistics::class);

        Livewire::component('mailcoach::send-test', SendTest::class);
        Livewire::component('mailcoach::data-table', DataTable::class);

        Livewire::component('mailcoach::dashboard', Mailcoach::getLivewireClass('dashboard', Dashboard::class));
        Livewire::component('mailcoach::dashboard-chart', DashboardChart::class);

        // Audience
        Livewire::component('mailcoach::create-list', Mailcoach::getLivewireClass('create-list', CreateList::class));
        Livewire::component('mailcoach::lists', Mailcoach::getLivewireClass('lists', Lists::class));
        Livewire::component('mailcoach::list-summary', Mailcoach::getLivewireClass('list-summary', ListSummary::class));
        Livewire::component('mailcoach::list-settings', Mailcoach::getLivewireClass('list-settings', ListSettings::class));
        Livewire::component('mailcoach::list-onboarding', Mailcoach::getLivewireClass('list-onboarding', ListOnboarding::class));
        Livewire::component('mailcoach::list-mailers', Mailcoach::getLivewireClass('list-mailers', ListMailers::class));
        Livewire::component('mailcoach::create-segment', Mailcoach::getLivewireClass('create-segment', CreateSegment::class));
        Livewire::component('mailcoach::segments', Mailcoach::getLivewireClass('segments', Segments::class));
        Livewire::component('mailcoach::segment', Mailcoach::getLivewireClass('segment', Segment::class));
        Livewire::component('mailcoach::segment-subscribers', Mailcoach::getLivewireClass('segment-subscribers', SegmentSubscribers::class));
        Livewire::component('mailcoach::create-subscriber', Mailcoach::getLivewireClass('create-subscriber', CreateSubscriber::class));
        Livewire::component('mailcoach::subscribers', Mailcoach::getLivewireClass('subscribers', Subscribers::class));
        Livewire::component('mailcoach::subscriber', Mailcoach::getLivewireClass('subscriber', Subscriber::class));
        Livewire::component('mailcoach::subscriber-sends', Mailcoach::getLivewireClass('subscriber-sends', SubscriberSends::class));
        Livewire::component('mailcoach::create-tag', Mailcoach::getLivewireClass('create-tag', CreateTag::class));
        Livewire::component('mailcoach::tags', Mailcoach::getLivewireClass('tags', Tags::class));
        Livewire::component('mailcoach::tag', Mailcoach::getLivewireClass('tag', Tag::class));

        // Automations
        Livewire::component('mailcoach::create-automation', Mailcoach::getLivewireClass('create-automation', CreateAutomation::class));
        Livewire::component('mailcoach::automations', Mailcoach::getLivewireClass('automations', Automations::class));
        Livewire::component('mailcoach::automation-settings', Mailcoach::getLivewireClass('automation-settings', AutomationSettings::class));
        Livewire::component('mailcoach::automation-actions', Mailcoach::getLivewireClass('automation-actions', AutomationActions::class));
        Livewire::component('mailcoach::automation-run', Mailcoach::getLivewireClass('automation-run', RunAutomation::class));
        Livewire::component('mailcoach::create-automation-mail', Mailcoach::getLivewireClass('create-automation-mail', CreateAutomationMail::class));
        Livewire::component('mailcoach::automation-mails', Mailcoach::getLivewireClass('automation-mails', AutomationMails::class));
        Livewire::component('mailcoach::automation-mail-summary', Mailcoach::getLivewireClass('automation-mail-summary', AutomationMailSummary::class));
        Livewire::component('mailcoach::automation-mail-settings', Mailcoach::getLivewireClass('automation-mail-settings', AutomationMailSettings::class));
        Livewire::component('mailcoach::automation-mail-clicks', Mailcoach::getLivewireClass('automation-mail-clicks', AutomationMailClicks::class));
        Livewire::component('mailcoach::automation-mail-opens', Mailcoach::getLivewireClass('automation-mail-opens', AutomationMailOpens::class));
        Livewire::component('mailcoach::automation-mail-unsubscribes', Mailcoach::getLivewireClass('automation-mail-unsubscribes', AutomationMailUnsubscribes::class));
        Livewire::component('mailcoach::automation-mail-outbox', Mailcoach::getLivewireClass('automation-mail-outbox', AutomationMailOutbox::class));

        // Campaigns
        Livewire::component('mailcoach::create-campaign', Mailcoach::getLivewireClass('create-campaign', CreateCampaign::class));
        Livewire::component('mailcoach::campaigns', Mailcoach::getLivewireClass('campaigns', Campaigns::class));
        Livewire::component('mailcoach::create-template', Mailcoach::getLivewireClass('create-template', CreateTemplate::class));
        Livewire::component('mailcoach::templates', Mailcoach::getLivewireClass('templates', Templates::class));
        Livewire::component('mailcoach::template', Mailcoach::getLivewireClass('template', Template::class));
        Livewire::component('mailcoach::campaign-settings', Mailcoach::getLivewireClass('campaign-settings', CampaignSettings::class));
        Livewire::component('mailcoach::campaign-delivery', Mailcoach::getLivewireClass('campaign-delivery', CampaignDelivery::class));
        Livewire::component('mailcoach::campaign-summary', Mailcoach::getLivewireClass('campaign-summary', CampaignSummary::class));
        Livewire::component('mailcoach::campaign-clicks', Mailcoach::getLivewireClass('campaign-clicks', CampaignClicks::class));
        Livewire::component('mailcoach::campaign-opens', Mailcoach::getLivewireClass('campaign-opens', CampaignOpens::class));
        Livewire::component('mailcoach::campaign-unsubscribes', Mailcoach::getLivewireClass('campaign-unsubscribes', CampaignUnsubscribes::class));
        Livewire::component('mailcoach::campaign-outbox', Mailcoach::getLivewireClass('campaign-outbox', CampaignOutbox::class));

        // TransactionalMails
        Livewire::component('mailcoach::create-transactional-template', Mailcoach::getLivewireClass('create-transactional-template', CreateTransactionalTemplate::class));
        Livewire::component('mailcoach::transactional-mails', Mailcoach::getLivewireClass('transactional-mails', TransactionalMails::class));
        Livewire::component('mailcoach::transactional-mail-templates', Mailcoach::getLivewireClass('transactional-mail-templates', TransactionalTemplates::class));
        Livewire::component('mailcoach::transactional-mail-template-content', Mailcoach::getLivewireClass('transactional-mail-template-content', TransactionalTemplateContent::class));
        Livewire::component('mailcoach::transactional-mail-template-settings', Mailcoach::getLivewireClass('transactional-mail-template-settings', TransactionalTemplateSettings::class));
        Livewire::component('mailcoach::transactional-mail-content', Mailcoach::getLivewireClass('transactional-mail-content', TransactionalMailContent::class));
        Livewire::component('mailcoach::transactional-mail-performance', Mailcoach::getLivewireClass('transactional-mail-performance', TransactionalMailPerformance::class));
        Livewire::component('mailcoach::transactional-mail-resend', Mailcoach::getLivewireClass('transactional-mail-resend', TransactionalMailResend::class));

        Livewire::component('mailcoach::export', Export::class);
        Livewire::component('mailcoach::import', Import::class);

        config()->set('livewire.temporary_file_upload.rules', [
            'file|max:102400', // 100 MB max
        ]);

        return $this;
    }

    private function bootEvents()
    {
        Event::listen(CampaignSentEvent::class, SendCampaignSentEmail::class);
        Event::listen(WebhookCallProcessedEvent::class, SetWebhookCallProcessedAt::class);
        Event::listen(MessageSending::class, StoreTransactionalMail::class);
        Event::listen(CampaignOpenedEvent::class, AddCampaignOpenedTag::class);
        Event::listen(CampaignLinkClickedEvent::class, AddCampaignClickedTag::class);
        Event::listen(AutomationMailOpenedEvent::class, AddAutomationMailOpenedTag::class);
        Event::listen(AutomationMailLinkClickedEvent::class, AddAutomationMailClickedTag::class);

        return $this;
    }

    private function bootTriggers(): self
    {
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

    protected function registerDeprecatedApiGuard(): self
    {
        if (config('auth.guards.api')) {
            return $this;
        }

        config()->set('auth.guards.api', [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ]);

        return $this;
    }

    protected function bootSpotlight(): self
    {
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

        return $this;
    }
}
