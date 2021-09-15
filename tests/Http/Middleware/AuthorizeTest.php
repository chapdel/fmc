<?php

use \Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Http\App\Middleware\Authorize;
use Spatie\Mailcoach\Tests\TestCase;


beforeEach(function () {
    test()->withExceptionHandling();

    Route::get('login', function () {
        return 'you should log in';
    })->name('login');

    Route::middleware(Authorize::class)->group(function () {
        Route::get('test', fn () => 'ok');
    });
});

it('can authorize', function () {
    test()->get('test')->assertRedirect('/login');

    Gate::define('viewMailcoach', fn ($user) => $user->email === 'john@example.com');

    test()->actingAs(getUser('john@example.com'));
    test()->get('test')->assertStatus(200);

    test()->actingAs(getUser('another-user@example.com'));
    test()->get('test')->assertRedirect('/login');

    config()->set('mailcoach.redirect_unauthorized_users_to_route', '');
    test()->get('test')->assertStatus(403);
});

// Helpers
function getUser(string $email): User
{
    $user = new User();
    $user->email = $email;

    return $user;
}
