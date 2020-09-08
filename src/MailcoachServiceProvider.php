<?php

namespace Spatie\Mailcoach;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Components\DateTimeFieldComponent;
use Spatie\Mailcoach\Components\FilterComponent;
use Spatie\Mailcoach\Components\ReplacerHelpTextsComponent;
use Spatie\Mailcoach\Components\SearchComponent;
use Spatie\Mailcoach\Components\THComponent;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Http\App\Controllers\HomeController;
use Spatie\Mailcoach\Http\App\ViewComposers\FooterComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\IndexComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\QueryStringComposer;
use Spatie\Mailcoach\Listeners\SendCampaignSentEmail;
use Spatie\Mailcoach\Support\HttpClient;
use Spatie\Mailcoach\Support\Version;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\QueryString\QueryString;

class MailcoachServiceProvider extends EventServiceProvider
{
    use UsesMailcoachModels;

    public function boot()
    {
        parent::boot();

        $this
            ->bootCarbon()
            ->bootCommands()
            ->bootGate()
            ->bootPublishables()
            ->bootRoutes()
            ->bootSupportMacros()
            ->bootTranslations()
            ->bootViews()
            ->registerEventListeners();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mailcoach.php', 'mailcoach');

        $this->app->singleton(QueryString::class, fn () => new QueryString(urldecode($this->app->request->getRequestUri())));

        $this->app->singleton(Version::class, function () {
            $httpClient = new HttpClient();

            return new Version($httpClient);
        });
    }

    protected function bootCarbon(): self
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Date::macro('toMailcoachFormat', fn () => self::this()->copy()->format($mailcoachFormat));

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
        Gate::define('viewMailcoach', fn ($user = null) => app()->environment('local'));

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

        if (! class_exists('CreateEmailCampaignTables')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailcoach_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailcoach_tables.php'),
            ], 'mailcoach-migrations');
        }

        if (! class_exists('CreateMediaTable')) {
            $this->publishes([
                __DIR__ . '/../../laravel-medialibrary/database/migrations/create_media_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_media_table.php'),
            ], 'mailcoach-migrations');
        }

        return $this;
    }

    protected function bootRoutes(): self
    {
        Route::macro('mailcoach', function (string $url = '') {
            Route::get($url, '\\'.HomeController::class)->name('mailcoach.home');

            Route::prefix($url)->group(function () {
                Route::prefix('')->group(__DIR__ . '/../routes/mailcoach-api.php');
                Route::middleware(config('mailcoach.middleware'))->group(__DIR__ . '/../routes/mailcoach-ui.php');
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

        if (config("mailcoach.views.use_blade_components", true)) {
            $this->bootBladeComponents();
        }

        return $this;
    }

    protected function bootTranslations(): self
    {
        $this->loadJSONTranslationsFrom(__DIR__ . '/../resources/lang/');

        return $this;
    }

    protected function bootBladeComponents(): self
    {
        Blade::component('mailcoach::app.components.form.checkboxField', 'checkbox-field');
        Blade::component('mailcoach::app.components.form.radioField', 'radio-field');
        Blade::component('mailcoach::app.components.form.formButton', 'form-button');
        Blade::component('mailcoach::app.components.form.selectField', 'select-field');
        Blade::component('mailcoach::app.components.form.tagsField', 'tags-field');
        Blade::component('mailcoach::app.components.form.textField', 'text-field');
        Blade::component('mailcoach::app.components.form.htmlField', 'html-field');
        Blade::component('mailcoach::app.components.form.dateField', 'date-field');
        Blade::component(DateTimeFieldComponent::class, 'date-time-field');

        Blade::component('mailcoach::app.components.modal.modal', 'modal');

        Blade::component('mailcoach::app.components.table.tableStatus', 'table-status');
        Blade::component(THComponent::class, 'th');

        Blade::component('mailcoach::app.components.filters.filters', 'filters');
        Blade::component(FilterComponent::class, 'filter');

        Blade::component(SearchComponent::class, 'search');
        Blade::component('mailcoach::app.components.statistic', 'statistic');
        Blade::component('mailcoach::app.components.navigationItem', 'navigation-item');
        Blade::component('mailcoach::app.components.iconLabel', 'icon-label');

        Blade::component('mailcoach::app.components.help', 'help');
        Blade::component('mailcoach::app.components.counter', 'counter');

        Blade::component(ReplacerHelpTextsComponent::class, 'replacer-help-texts');

        return $this;
    }

    protected function registerEventListeners(): self
    {
        Event::listen(CampaignSentEvent::class, function (CampaignSentEvent $event) {
            (new SendCampaignSentEmail())->handle($event);
        });

        return $this;
    }
}
