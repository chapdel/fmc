<?php

namespace Spatie\Mailcoach\Tests;

use CreateJobBatchesTable;
use CreateMailcoachTables;
use CreateMediaTable;
use CreateUsersTable;
use CreateWebhookCallsTable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Feed\FeedServiceProvider;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;
use Spatie\TestTime\TestTime;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Route::mailcoach('mailcoach');

        $this->withoutExceptionHandling();

        Redis::flushAll();

        Gate::define('viewMailcoach', fn () => true);

        TestTime::freeze();

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                return 'Spatie\\Mailcoach\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
            }
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
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

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
        app(MailcoachServiceProvider::class, ['app' => $this->app])->boot();
    }
}
