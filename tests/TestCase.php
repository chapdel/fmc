<?php

namespace Spatie\Mailcoach\Tests;

use CreatePersonalAccessTokensTable;
use CreateUsersTable;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Laravel\Sanctum\SanctumServiceProvider;
use Livewire\LivewireServiceProvider;
use LivewireUI\Spotlight\SpotlightServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Feed\FeedServiceProvider;
use Spatie\Flash\Flash;
use Spatie\LaravelCipherSweet\LaravelCipherSweetServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\MailcoachServiceProvider;
use Spatie\MailcoachEditor\MailcoachEditorServiceProvider;
use Spatie\MailcoachMailgunFeedback\MailcoachMailgunFeedbackServiceProvider;
use Spatie\MailcoachMarkdownEditor\MailcoachMarkdownEditorServiceProvider;
use Spatie\MailcoachPostmarkFeedback\MailcoachPostmarkFeedbackServiceProvider;
use Spatie\MailcoachSendgridFeedback\MailcoachSendgridFeedbackServiceProvider;
use Spatie\MailcoachSesFeedback\MailcoachSesFeedbackServiceProvider;
use Spatie\MailcoachUnlayer\MailcoachUnlayerServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Navigation\NavigationServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;
use function Spatie\Snapshots\assertMatchesHtmlSnapshot;
use Spatie\TestTime\TestTime;
use Spatie\WebhookServer\WebhookServerServiceProvider;

abstract class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;
    use UsesMailcoachModels;

    protected function setUp(): void
    {
        parent::setUp();

        Route::mailcoach('mailcoach');

        config()->set('auth.providers.users.model', User::class);
        config()->set('mailcoach.timezone', null);

        $this->withoutExceptionHandling();

        Redis::flushAll();

        Gate::define('viewMailcoach', fn () => true);

        TestTime::freeze();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\Mailcoach\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        View::addLocation(__DIR__.'/views');

        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);
    }

    protected function tearDown(): void
    {
        cache()->forget('automation-triggers');

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCipherSweetServiceProvider::class,
            SpotlightServiceProvider::class,
            RayServiceProvider::class,
            LivewireServiceProvider::class,
            MailcoachServiceProvider::class,
            FeedServiceProvider::class,
            MediaLibraryServiceProvider::class,
            QueryBuilderServiceProvider::class,
            NavigationServiceProvider::class,
            SanctumServiceProvider::class,
            FeedServiceProvider::class,

            MailcoachSesFeedbackServiceProvider::class,
            MailcoachMailgunFeedbackServiceProvider::class,
            MailcoachSendgridFeedbackServiceProvider::class,
            MailcoachPostmarkFeedbackServiceProvider::class,
            MailcoachUnlayerServiceProvider::class,
            MailcoachEditorServiceProvider::class,
            MailcoachMarkdownEditorServiceProvider::class,
            WebhookServerServiceProvider::class,
        ];
    }

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            //include_once __DIR__.'/database/migrations/create_users_table.php.stub';
            //(new CreateUsersTable())->up();

            $blindIndexes = include __DIR__.'/../vendor/spatie/laravel-ciphersweet/database/migrations/create_blind_indexes_table.php';
            $blindIndexes->up();

            /*
            $migration = include_once __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';
            $migration->up();
            */

            /*
            include_once __DIR__.'/../vendor/laravel/sanctum/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php';
            (new CreatePersonalAccessTokensTable())->up();
            */

            $passwordResets = include_once __DIR__.'/../vendor/laravel/ui/stubs/migrations/2014_10_12_100000_create_password_resets_table.php';
            $passwordResets->up();

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

        $user->createToken('test');

        $this->actingAs($user, $guard);
    }

    public function assertMatchesHtmlSnapshotWithoutWhitespace(string $content)
    {
        $contentWithoutWhitespace = preg_replace('/\s/', '', $content);

        $contentWithoutWhitespace = str_replace(PHP_EOL, '', $contentWithoutWhitespace);

        assertMatchesHtmlSnapshot($contentWithoutWhitespace);
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

    public function stub(string $path): string
    {
        $path = __DIR__."/stubs/{$path}";

        return file_get_contents($path);
    }
}
