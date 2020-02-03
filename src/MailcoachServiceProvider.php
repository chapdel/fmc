<?php

namespace Spatie\Mailcoach;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Spatie\BladeX\Facades\BladeX;
use Spatie\Mailcoach\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Commands\DeleteOldUploadsCommand;
use Spatie\Mailcoach\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Commands\SendCampaignSummaryMailCommand;
use Spatie\Mailcoach\Commands\SendEmailListSummaryMailCommand;
use Spatie\Mailcoach\Commands\SendScheduledCampaignsCommand;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Http\App\Controllers\HomeController;
use Spatie\Mailcoach\Http\App\ViewComposers\FooterComposer;
use Spatie\Mailcoach\Http\App\ViewComposers\QueryStringComposer;
use Spatie\Mailcoach\Http\App\ViewModels\BladeX\DateTimeFieldViewModel;
use Spatie\Mailcoach\Http\App\ViewModels\BladeX\FilterViewModel;
use Spatie\Mailcoach\Http\App\ViewModels\BladeX\ReplacerHelpTextsViewModel;
use Spatie\Mailcoach\Http\App\ViewModels\BladeX\SearchViewModel;
use Spatie\Mailcoach\Http\App\ViewModels\BladeX\THViewModel;
use Spatie\Mailcoach\Listeners\SendCampaignSentEmail;
use Spatie\Mailcoach\Support\HttpClient;
use Spatie\Mailcoach\Support\Version;
use Spatie\QueryString\QueryString;

class MailcoachServiceProvider extends EventServiceProvider
{
    protected $listen = [
        CampaignSentEvent::class => [
            SendCampaignSentEmail::class,
        ]
    ];

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
            ->bootViews();
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

    protected function bootCarbon()
    {
        $mailcoachFormat = config('mailcoach.date_format');

        Carbon::macro('toMailcoachFormat', fn () => self::this()->copy()->format($mailcoachFormat));

        return $this;
    }

    protected function bootCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CalculateStatisticsCommand::class,
                SendScheduledCampaignsCommand::class,
                SendCampaignSummaryMailCommand::class,
                SendEmailListSummaryMailCommand::class,
                RetryPendingSendsCommand::class,
                DeleteOldUnconfirmedSubscribersCommand::class,
                DeleteOldUploadsCommand::class,
            ]);
        }

        return $this;
    }

    protected function bootSupportMacros()
    {
        if (!Collection::hasMacro('paginate')) {
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

        if (!Str::hasMacro('shortNumber')) {
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

    protected function bootGate()
    {
        Gate::define('viewMailcoach', fn ($user = null) => app()->environment('local'));

        return $this;
    }

    protected function bootPublishables()
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

        if (!class_exists('CreateEmailCampaignTables')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailcoach_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailcoach_tables.php'),
            ], 'mailcoach-migrations');
        }

        if (!class_exists('CreateMediaTable')) {
            $this->publishes([
                __DIR__ . '/../../laravel-medialibrary/database/migrations/create_media_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_media_table.php'),
            ], 'mailcoach-migrations');
        }

        return $this;
    }

    protected function bootRoutes()
    {
        Route::macro('mailcoach', function (string $url = '') {
            Route::get($url, HomeController::class)->name('mailcoach.home');

            Route::prefix($url)->group(function () {
                Route::prefix('')->group(__DIR__ . '/../routes/mailcoach-api.php');
                Route::middleware(config('mailcoach.middleware'))->group(__DIR__ . '/../routes/mailcoach-ui.php');
            });
        });

        return $this;
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mailcoach');

        View::composer('mailcoach::*', QueryStringComposer::class);
        View::composer('mailcoach::app.layouts.partials.footer', FooterComposer::class);

        BladeX::prefix('x');

        BladeX::component('mailcoach::app.components.form.checkboxField', 'checkbox-field');
        BladeX::component('mailcoach::app.components.form.radioField', 'radio-field');
        BladeX::component('mailcoach::app.components.form.formButton', 'form-button');
        BladeX::component('mailcoach::app.components.form.selectField', 'select-field');
        BladeX::component('mailcoach::app.components.form.tagsField', 'tags-field');
        BladeX::component('mailcoach::app.components.form.textField', 'text-field');
        BladeX::component('mailcoach::app.components.form.htmlField', 'html-field');
        BladeX::component('mailcoach::app.components.form.editorField', 'editor-field')
            ->viewModel(ReplacerHelpTextsViewModel::class);
        BladeX::component('mailcoach::app.components.form.dateField', 'date-field');
        BladeX::component('mailcoach::app.components.form.dateTimeField', 'date-time-field')
            ->viewModel(DateTimeFieldViewModel::class);

        BladeX::component('mailcoach::app.components.modal.modal', 'modal');

        BladeX::component('mailcoach::app.components.table.tableStatus', 'table-status');
        BladeX::component('mailcoach::app.components.table.th', 'th')
            ->viewModel(THViewModel::class);

        BladeX::component('mailcoach::app.components.filters.filters', 'filters');
        BladeX::component('mailcoach::app.components.filters.filter', 'filter')
            ->viewModel(FilterViewModel::class);

        BladeX::component('mailcoach::app.components.search', 'search')
            ->viewModel(SearchViewModel::class);
        BladeX::component('mailcoach::app.components.statistic', 'statistic');
        BladeX::component('mailcoach::app.components.navigationItem', 'navigation-item');
        BladeX::component('mailcoach::app.components.iconLabel', 'icon-label');

        BladeX::component('mailcoach::app.components.help', 'help');
        BladeX::component('mailcoach::app.components.counter', 'counter');

        BladeX::component('mailcoach::app.components.replacerHelpTexts', 'replacer-help-texts')
            ->viewModel(ReplacerHelpTextsViewModel::class);

        return $this;
    }

    protected function bootTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'mailcoach');

        return $this;
    }
}
