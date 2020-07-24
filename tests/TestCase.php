<?php

namespace Spatie\Mailcoach\Tests;

use CreateMailcoachTables;
use CreateMediaTable;
use CreateUsersTable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Feed\FeedServiceProvider;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\Mailcoach\Models\Send;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\TestTime\TestTime;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../database/factories');

        Route::mailcoach('mailcoach');

        $this->withoutExceptionHandling();

        Redis::flushAll();

        Gate::define('viewMailcoach', fn () => true);

        TestTime::freeze();
    }

    protected function getPackageProviders($app)
    {
        return [
            MailcoachServiceProvider::class,
            FeedServiceProvider::class,
            MediaLibraryServiceProvider::class,
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
        $user = factory(User::class)->create();

        $this->actingAs($user, $guard);
    }

    public function assertMatchesHtmlSnapshotWithoutWhitespace(string $content)
    {
        $contentWithoutWhitespace = preg_replace('/\s/', '', $content);

        $contentWithoutWhitespace = str_replace(PHP_EOL, '', $contentWithoutWhitespace);

        $this->assertMatchesHtmlSnapshot($contentWithoutWhitespace);
    }
}
