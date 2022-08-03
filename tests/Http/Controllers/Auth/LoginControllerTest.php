<?php

use Spatie\Mailcoach\Http\Auth\Controllers\LoginController;
use Spatie\Mailcoach\Domain\Settings\Models\User;

beforeEach(function() {
    $this->user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('my-password'),
    ]);
});

it('can login', function() {
    $this->post(
        action([LoginController::class, 'login']),
        [
            'email' => 'john@example.com',
            'password' => 'my-password',
        ]
    )
        ->assertRedirect('/campaigns');

    $this->assertAuthenticatedAs($this->user);
});

it('will not login when providing a wrong password', function() {
    $this->withExceptionHandling();

    $this
        ->post(action([LoginController::class, 'login']), [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('email');

    $this->assertFalse($this->isAuthenticated());
});
