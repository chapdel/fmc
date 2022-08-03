<?php

use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\App\Controllers\Settings\Account\PasswordController;

it('can change the password of the authenticated user', function () {
    $currentPassword = 'current-password';
    $newPassword = 'my-new-password';

    $user = User::factory()->create([
        'password' => bcrypt($currentPassword),
    ]);

    $this->actingAs($user);

    $this
        ->put(action([PasswordController::class, 'update']), [
            'current_password' => $currentPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(action([PasswordController::class, 'index']));

    $this->assertTrue(auth()->validate([
        'email' => auth()->user()->email,
        'password' => $newPassword,
    ]));
});

it('will fail if the current password is not correct', function () {
    $newPassword = 'my-new-password';

    $this->authenticate();

    $this->withExceptionHandling();

    $this
        ->put(action([PasswordController::class, 'update']), [
            'current_password' => 'wrong-current-password',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])
        ->assertSessionHasErrors('current_password');
});
