<?php

use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\Auth\Controllers\LoginController;
use Spatie\Mailcoach\Http\Auth\Controllers\LogoutController;

it('can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->assertTrue($this->isAuthenticated());

    $this
        ->post(action(LogoutController::class))
        ->assertRedirect(action([LoginController::class, 'showLoginForm']));
    $this->assertFalse($this->isAuthenticated());
});
