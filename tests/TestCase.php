<?php

namespace Spatie\Mailcoach\Tests;

use AllowDynamicProperties;
use Dotenv\Dotenv;
use Filament\Actions\ActionsServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Feed\FeedServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Navigation\NavigationServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;
use Spatie\TestTime\TestTime;
use Spatie\WebhookServer\WebhookServerServiceProvider;

#[AllowDynamicProperties]
abstract class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;
    use UsesMailcoachModels;

    protected function setUp(): void
    {
        parent::setUp();

        Route::mailcoach('mailcoach');

        app('router')->getRoutes()->refreshNameLookups();

        config()->set('mailcoach.timezone', null);

        $this->withoutExceptionHandling();

        Redis::flushAll();
        Cache::clear();

        Gate::define('viewMailcoach', fn () => true);
        Gate::after(function () {
            return true;
        });

        TestTime::freeze();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\Mailcoach\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        View::addLocation(__DIR__.'/views');
    }

    protected function tearDown(): void
    {
        cache()->forget('automation-triggers');

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            //SpotlightServiceProvider::class,
            RayServiceProvider::class,
            LivewireServiceProvider::class,
            MailcoachServiceProvider::class,
            FeedServiceProvider::class,
            MediaLibraryServiceProvider::class,
            QueryBuilderServiceProvider::class,
            NavigationServiceProvider::class,
            FeedServiceProvider::class,

            // Filament
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,

            WebhookServerServiceProvider::class,
        ];
    }

    protected function refreshTestDatabase(): void
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('vendor:publish', ['--tag' => 'mailcoach-migrations', '--force' => true])->run();
            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            $migration = include __DIR__.'/../vendor/laravel/ui/stubs/migrations/2014_10_12_100000_create_password_resets_table.php';
            $migration->up();

            $migration = include __DIR__.'/../vendor/orchestra/testbench-core/laravel/migrations/2014_10_12_100000_testbench_create_password_reset_tokens_table.php';
            $migration->up();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql', [
            'driver' => 'mysql',
            'database' => 'mailcoach_tests',
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => env('DB_PASSWORD', ''),
            'prefix' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
    }

    protected function simulateUnsubscribes(Collection $sends): void
    {
        $sends->each(function (Send $send) {
            $this
                ->post(action([UnsubscribeController::class, 'confirm'], [$send->subscriber->uuid, $send->uuid]));
        });
    }

    public function authenticate(): void
    {
        $user = UserFactory::new()->create();

        $this->actingAs($user);
    }

    public function refreshServiceProvider(): void
    {
        // We need to do this since the service provider loads from the database
        app(MailcoachServiceProvider::class, ['app' => $this->app])
            ->register()
            ->boot();
    }

    public function processQueuedJobs(): void
    {
        foreach (Queue::pushedJobs() as $jobs) {
            foreach ($jobs as $job) {
                $job['job']->handle();
            }
        }
    }

    public function stub(string $path): string
    {
        $path = __DIR__."/stubs/{$path}";

        return file_get_contents($path);
    }

    protected function loadEnvironmentVariables(): void
    {
        if (file_exists(__DIR__.'/../.env')) {
            $dotEnv = Dotenv::createImmutable(__DIR__.'/..');

            $dotEnv->load();
        }
    }
}
