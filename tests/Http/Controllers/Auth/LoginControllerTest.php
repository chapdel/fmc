<?php

use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\Auth\Controllers\LoginController;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('my-password'),
    ]);
});

it('can login', function () {
    $this->post(
        action([LoginController::class, 'login']),
        [
            'email' => 'john@example.com',
            'password' => 'my-password',
        ]
    )
        ->assertRedirect('/mailcoach/dashboard');

    $this->assertAuthenticatedAs($this->user);
});

it('redirects to configured route', function () {
    config()->set('mailcoach.redirect_home', 'mailcoach.campaigns');

    $this->post(
        action([LoginController::class, 'login']),
        [
            'email' => 'john@example.com',
            'password' => 'my-password',
        ]
    )
        ->assertRedirect('/mailcoach/campaigns');

    $this->assertAuthenticatedAs($this->user);
});

it('will redirect to intended url', function () {
    session()->put('url.intended', '/mailcoach/templates');

    $this->post(
        action([LoginController::class, 'login']),
        [
            'email' => 'john@example.com',
            'password' => 'my-password',
        ]
    )
        ->assertRedirect('/mailcoach/templates');

    $this->assertAuthenticatedAs($this->user);
});

it('will not login when providing a wrong password', function () {
    $this->withExceptionHandling();

    $this
        ->post(action([LoginController::class, 'login']), [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('email');

    $this->assertFalse($this->isAuthenticated());
});
