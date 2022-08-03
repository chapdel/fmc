<?php

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\Auth\Controllers\ForgotPasswordController;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('my-password'),
    ]);

    $this->resetUrl = action([ForgotPasswordController::class, 'sendResetLinkEmail']);

    Notification::fake();
});

it('can send a reset password notification', function () {
    $this
        ->post($this->resetUrl, ['email' => 'john@example.com'])
        ->assertRedirect();

    Notification::assertSentTo($this->user, ResetPassword::class);
});

it('will not send a password reset notification when given a wrong email address', function () {
    $this
        ->post($this->resetUrl, ['email' => 'non-existing@example.com'])
        ->assertRedirect();

    Notification::assertNotSentTo($this->user, ResetPassword::class);
});
