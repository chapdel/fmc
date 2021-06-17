<?php

namespace Spatie\Mailcoach\Tests;

use CreateJobBatchesTable;
use CreateMailcoachTables;
use CreateMediaTable;
use CreateUsersTable;
use CreateWebhookCallsTable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Feed\FeedServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;
use Spatie\TestTime\TestTime;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        cache()->forget('automation-triggers');

        Route::mailcoach('mailcoach');

        $this->withoutExceptionHandling();

        Redis::flushAll();

        Gate::define('viewMailcoach', fn () => true);

        TestTime::freeze();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\Mailcoach\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        View::addLocation(__DIR__ . '/views');
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            MailcoachServiceProvider::class,
            FeedServiceProvider::class,
            MediaLibraryServiceProvider::class,
            QueryBuilderServiceProvider::class,
            RayServiceProvider::class,
        ];
    }

    protected function refreshTestDatabase()
    {
        if (! Schema::hasTable('mailcoach_campaigns')) {
            Schema::dropAllTables();

            include_once __DIR__.'/../database/migrations/create_mailcoach_tables.php.stub';
            (new CreateMailcoachTables())->up();

            include_once __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';
            (new CreateMediaTable())->up();

            include_once __DIR__.'/database/migrations/create_users_table.php.stub';
            (new CreateUsersTable())->up();

            include_once __DIR__.'/../database/migrations/create_webhook_calls_table.php.stub';
            (new CreateWebhookCallsTable())->up();

            include_once __DIR__.'/../database/migrations/create_job_batches_table.php.stub';
            (new CreateJobBatchesTable())->up();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function getEnvironmentSetUp($app)
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

    protected function simulateUnsubscribes(Collection $sends)
    {
        $sends->each(function (Send $send) {
            $this
                ->post(action([UnsubscribeController::class, 'confirm'], [$send->subscriber->uuid, $send->uuid]));
        });
    }

    public function authenticate(string $guard = null)
    {
        $user = UserFactory::new()->create();

        $this->actingAs($user, $guard);
    }

    public function assertMatchesHtmlSnapshotWithoutWhitespace(string $content)
    {
        $contentWithoutWhitespace = preg_replace('/\s/', '', $content);

        $contentWithoutWhitespace = str_replace(PHP_EOL, '', $contentWithoutWhitespace);

        $this->assertMatchesHtmlSnapshot($contentWithoutWhitespace);
    }

    public function refreshServiceProvider()
    {
        // We need to do this since the service provider loads from the database
        app(MailcoachServiceProvider::class, ['app' => $this->app])
            ->register()
            ->boot();
    }

    public function processQueuedJobs()
    {
        foreach (Queue::pushedJobs() as $job) {
            $job[0]['job']->handle();
        }
    }
}
