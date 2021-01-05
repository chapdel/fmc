<?php

namespace Spatie\Mailcoach;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\FilterComponent;
use Spatie\Mailcoach\Components\MailPersonsComponent;
use Spatie\Mailcoach\Components\ReplacerHelpTextsComponent;
use Spatie\Mailcoach\Components\SearchComponent;
use Spatie\Mailcoach\Components\THComponent;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AddTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\CampaignActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\EnsureTagsExistActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\RemoveTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationBuilder;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components\TagChainComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\DateTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagAddedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagRemovedTriggerComponent;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\CleanupProcessedFeedbackCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Domain\Campaign\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddCampaignClickedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\AddCampaignOpenedTag;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SendCampaignSentEmail;
use Spatie\Mailcoach\Domain\Campaign\Listeners\SetWebhookCallProcessedAt;
use Spatie\Mailcoach\Domain\Shared\Support\Version;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Listeners\StoreTransactionalMail;
use Spatie\Mailcoach\Http\App\Controllers\HomeController;
use Spatie\Mailcoach\Http\App\ViewComposers\CampaignActionComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\FooterComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\IndexComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\QueryStringComposer;
use Spatie\QueryString\QueryString;

class MailcoachServiceProvider extends ServiceProvider
{
    use UsesMailcoachModels;

    public function boot()
    {
        $this
            ->bootCarbon()
            ->bootCommands()
            ->bootGate()
            ->bootPublishables()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootTranslations()
            ->bootViews()
            ->bootEvents()
            ->bootTriggers();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mailcoach.php', 'mailcoach');

        $this->app->singleton(QueryString::class, fn () => new QueryString(urldecode(request()->getRequestUri())));

        $this->app->singleton(Version::class, function () {
            return new Version();
        });
    }

    protected function bootCarbon(): self
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Date::macro(
            'toMailcoachFormat',
            fn () => self::this()->copy()->setTimezone(config('app.timezone'))->format($mailcoachFormat)
        );

        return $this;
    }

    protected function bootCommands(): self
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CalculateStatisticsCommand::class,
                SendScheduledCampaignsCommand::class,
                SendCampaignSummaryMailCommand::class,
                SendEmailListSummaryMailCommand::class,
                RetryPendingSendsCommand::class,
                DeleteOldUnconfirmedSubscribersCommand::class,
                CleanupProcessedFeedbackCommand::class,
                RunAutomationActionsCommand::class,
                RunAutomationTriggersCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootSupportMacros(): self
    {
        if (! Collection::hasMacro('paginate')) {
            Collection::macro('paginate', function (int $perPage = 15, string $pageName = 'page', int $page = null, int $total = null, array $options = []): LengthAwarePaginator {
                $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

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
        Gate::define('viewMailcoach', fn () => app()->environment('local'));

        return $this;
    }

    protected function bootPublishables(): self
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/mailcoach'),
        ], 'mailcoach-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/mailcoach'),
        ], 'mailcoach-lang');

        $this->publishes([
            __DIR__ . '/../config/mailcoach.php' => config_path('mailcoach.php'),
        ], 'mailcoach-config');

        $this->publishes([
            __DIR__ . '/../resources/dist' => public_path('vendor/mailcoach'),
        ], 'mailcoach-assets');

        if (! class_exists('CreateMailcoachTables')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailcoach_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailcoach_tables.php'),
            ], 'mailcoach-migrations');
        }

        if (! class_exists('CreateMediaTable')) {
            $this->publishes([
                __DIR__ . '/../../laravel-medialibrary/database/migrations/create_media_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_media_table.php'),
            ], 'mailcoach-migrations');
        }

        if (! class_exists('CreateWebhookCallsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_webhook_calls_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_webhook_calls_table.php'),
            ], 'mailcoach-migrations');
        }

        if (! class_exists('CreateJobBatchesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_job_batches_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_job_batches_table.php'),
            ], 'mailcoach-migrations');
        }

        return $this;
    }

    protected function bootRoutes(): self
    {
        Route::macro('mailcoach', function (string $url = '') {
            Route::get($url, '\\'.HomeController::class)->name('mailcoach.home');

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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mailcoach');

        View::composer('mailcoach::*', QueryStringComposer::class);
        View::composer('mailcoach::*.index', IndexComposer::class);

        View::composer('mailcoach::app.layouts.partials.footer', FooterComposer::class);

        View::composer('mailcoach::app.automations.partials.actions.campaignAction', CampaignActionComposer::class);

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
        Blade::component('mailcoach::app.components.form.selectField', 'mailcoach::select-field');
        Blade::component('mailcoach::app.components.form.tagsField', 'mailcoach::tags-field');
        Blade::component('mailcoach::app.components.form.textField', 'mailcoach::text-field');
        Blade::component('mailcoach::app.components.form.htmlField', 'mailcoach::html-field');
        Blade::component('mailcoach::app.components.form.dateField', 'mailcoach::date-field');
        Blade::component(DateTimeFieldComponent::class, 'mailcoach::date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'mailcoach::modal');

        Blade::component('mailcoach::app.components.table.tableStatus', 'mailcoach::table-status');
        Blade::component(THComponent::class, 'mailcoach::th');

        Blade::component('mailcoach::app.components.filters.filters', 'mailcoach::filters');
        Blade::component(FilterComponent::class, 'mailcoach::filter');

        Blade::component(SearchComponent::class, 'mailcoach::search');
        Blade::component('mailcoach::app.components.statistic', 'mailcoach::statistic');
        Blade::component('mailcoach::app.components.navigationItem', 'mailcoach::navigation-item');
        Blade::component('mailcoach::app.components.iconLabel', 'mailcoach::icon-label');

        Blade::component('mailcoach::app.components.help', 'mailcoach::help');
        Blade::component('mailcoach::app.components.counter', 'mailcoach::counter');
        Blade::component(MailPersonsComponent::class, 'mailcoach::mail-persons');


        Blade::component(ReplacerHelpTextsComponent::class, 'mailcoach::replacer-help-texts');

        return $this;
    }

    protected function bootLivewireComponents(): self
    {
        Livewire::component('automation-builder', AutomationBuilder::class);

        Livewire::component('campaign-action', CampaignActionComponent::class);
        Livewire::component('add-tags-action', AddTagsActionComponent::class);
        Livewire::component('remove-tags-action', RemoveTagsActionComponent::class);
        Livewire::component('wait-action', WaitActionComponent::class);
        Livewire::component('ensure-tags-exist-action', EnsureTagsExistActionComponent::class);
        Livewire::component('tag-chain', TagChainComponent::class);

        Livewire::component('date-trigger', DateTriggerComponent::class);
        Livewire::component('tag-added-trigger', TagAddedTriggerComponent::class);
        Livewire::component('tag-removed-trigger', TagRemovedTriggerComponent::class);

        return $this;
    }

    private function bootEvents()
    {
        Event::listen(CampaignSentEvent::class, SendCampaignSentEmail::class);
        Event::listen(WebhookCallProcessedEvent::class, SetWebhookCallProcessedAt::class);
        Event::listen(MessageSending::class, StoreTransactionalMail::class);
        Event::listen(CampaignOpenedEvent::class, AddCampaignOpenedTag::class);
        Event::listen(CampaignLinkClickedEvent::class, AddCampaignClickedTag::class);

        return $this;
    }

    private function bootTriggers()
    {
        if (Schema::hasTable('mailcoach_automations')) {
            $automations = cache()->rememberForever('mailcoach-automations', function () {
                return Automation::all();
            });

            $automations->each(function (Automation $automation) {
                if ($automation->trigger) {
                    Event::subscribe($automation->trigger);
                }
            });
        }
    }
}
