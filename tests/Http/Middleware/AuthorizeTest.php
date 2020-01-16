<?php

namespace Spatie\Mailcoach\Tests\Http\Middleware;

use \Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\App\Middleware\Authorize;
use Spatie\Mailcoach\Tests\TestCase;

class AuthorizeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        Route::get('login', function () {
            return 'you should log in';
        })->name('login');

        Route::middleware(Authorize::class)->group(function () {
            Route::get('test', fn () => 'ok');
        });
    }

    /** @test */
    public function it_can_authorize()
    {
        $this->get('test')->assertRedirect('/login');

        Gate::define('viewMailcoach', fn ($user) => $user->email === 'john@example.com');

        $this->actingAs($this->getUser('john@example.com'));
        $this->get('test')->assertStatus(200);

        $this->actingAs($this->getUser('another-user@example.com'));
        $this->get('test')->assertRedirect('/login');

        config()->set('mailcoach.redirect_unauthorized_users_to_route', '');
        $this->get('test')->assertStatus(403);
    }

    protected function getUser(string $email): User
    {
        $user = new User();
        $user->email = $email;

        return $user;
    }
}
