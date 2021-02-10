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
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\FilterComponent;
use Spatie\Mailcoach\Components\MailPersonsComponent;
use Spatie\Mailcoach\Components\CampaignReplacerHelpTextsComponent;
use Spatie\Mailcoach\Components\SearchComponent;
use Spatie\Mailcoach\Components\THComponent;
use Spatie\Mailcoach\Components\TransactionalMailTemplateReplacerHelpTextsComponent;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationActionsCommand;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AddTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\CampaignActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\EnsureTagsExistActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\RemoveTagsActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationBuilder;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components\AutomationActionsComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components\AutomationSettingsComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components\TagChainComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\DateTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagAddedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\TagRemovedTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Triggers\WebhookTriggerComponent;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TriggeredByEvents;
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
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Controllers\HomeController;
use Spatie\Mailcoach\Http\App\ViewComposers\CampaignActionComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\FooterComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\IndexComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\QueryStringComposer;
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
                'create_job_batches_table',
                'create_mailcoach_tables',
                'create_webhook_calls_table',
            ])
            ->hasCommands([
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

    public function packageRegistered()
    {
        $this->app->singleton(QueryString::class, fn () => new QueryString(urldecode(request()->getRequestUri())));

        $this->app->singleton(Version::class, function () {
            return new Version();
        });
    }

    public function packageBooted()
    {
        $this
            ->bootCarbon()
            ->bootGate()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootTranslations()
            ->bootViews()
            ->bootEvents()
            ->bootTriggers();
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

    protected function bootRoutes(): self
    {
        Route::model('transactionalMailTemplate', TransactionalMailTemplate::class);

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
        Blade::component('mailcoach::app.components.form.fieldset', 'mailcoach::fieldset');
        Blade::component(DateTimeFieldComponent::class, 'mailcoach::date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'mailcoach::modal');

        Blade::component('mailcoach::app.components.table.tableStatus', 'mailcoach::table-status');
        Blade::component(THComponent::class, 'mailcoach::th');

        Blade::component('mailcoach::app.components.filters.filters', 'mailcoach::filters');
        Blade::component(FilterComponent::class, 'mailcoach::filter');

        Blade::component(SearchComponent::class, 'mailcoach::search');
        Blade::component('mailcoach::app.components.statistic', 'mailcoach::statistic');
        Blade::component('mailcoach::app.components.iconLabel', 'mailcoach::icon-label');
        Blade::component('mailcoach::app.components.healthIcon', 'mailcoach::health-icon');

        Blade::component('mailcoach::app.components.navigation.root', 'mailcoach::navigation');
        Blade::component('mailcoach::app.components.navigation.item', 'mailcoach::navigation-item');
        Blade::component('mailcoach::app.components.navigation.group', 'mailcoach::navigation-group');
        Blade::component('mailcoach::app.components.navigation.tabs', 'mailcoach::navigation-tabs');

        Blade::component('mailcoach::app.components.alert.help', 'mailcoach::help');
        Blade::component('mailcoach::app.components.alert.warning', 'mailcoach::warning');
        Blade::component('mailcoach::app.components.alert.error', 'mailcoach::error');
        Blade::component('mailcoach::app.components.alert.success', 'mailcoach::success');

        Blade::component('mailcoach::app.components.counter', 'mailcoach::counter');
        Blade::component(MailPersonsComponent::class, 'mailcoach::mail-persons');

        Blade::component('mailcoach::app.components.button.primary', 'mailcoach::button');
        Blade::component('mailcoach::app.components.button.secondary', 'mailcoach::button-secondary');
        Blade::component('mailcoach::app.components.button.cancel', 'mailcoach::button-cancel');

        Blade::component(CampaignReplacerHelpTextsComponent::class, 'mailcoach::campaign-replacer-help-texts');

        Blade::component(TransactionalMailTemplateReplacerHelpTextsComponent::class, 'mailcoach::transactional-mail-template-replacer-help-texts');

        Blade::component('mailcoach::app.components.dropdown', 'mailcoach::dropdown');

        Blade::component('mailcoach::app.layouts.app', 'mailcoach::layout');
        Blade::component('mailcoach::app.layouts.main', 'mailcoach::layout-main');
        Blade::component('mailcoach::app.automations.layouts.automation', 'mailcoach::layout-automation');
        Blade::component('mailcoach::app.campaigns.layouts.campaign', 'mailcoach::layout-campaign');
        Blade::component('mailcoach::app.emailLists.layouts.emailList', 'mailcoach::layout-list');
        Blade::component('mailcoach::app.emailLists.segments.layouts.segment', 'mailcoach::layout-segment');
        Blade::component('mailcoach::app.emailLists.subscribers.layouts.subscriber', 'mailcoach::layout-subscriber');
        Blade::component('mailcoach::app.transactionalMails.layouts.transactional', 'mailcoach::layout-transactional');
        Blade::component('mailcoach::app.transactionalMails.templates.layouts.template', 'mailcoach::layout-transactional-template');

        return $this;
    }

    protected function bootLivewireComponents(): self
    {
        Livewire::component('automation-actions', AutomationActionsComponent::class);
        Livewire::component('automation-settings', AutomationSettingsComponent::class);
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
        Livewire::component('webhook-trigger', WebhookTriggerComponent::class);

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
                /** @var \Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger|null $trigger */
                $trigger = $automation->trigger;

                if (! $trigger) {
                    return;
                }

                if (! $trigger instanceof TriggeredByEvents) {
                    return;
                }

                Event::subscribe($automation->trigger);
            });
        }
    }
}
