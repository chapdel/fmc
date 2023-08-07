<?php

use Illuminate\Foundation\Auth\User;

it('can change the password of the authenticated user', function () {
    $currentPassword = 'current-password';
    $newPassword = 'my-new-password';

    $user = User::factory()->create([
        'password' => bcrypt($currentPassword),
    ]);

    $this->actingAs($user);

    \Livewire\Livewire::test('mailcoach::password')
        ->set('current_password', $currentPassword)
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertTrue(auth()->validate([
        'email' => auth()->user()->email,
        'password' => $newPassword,
    ]));
});

it('will fail if the current password is not correct', function () {
    $newPassword = 'my-new-password';

    $this->authenticate();

    $this->withExceptionHandling();

    \Livewire\Livewire::test('mailcoach::password')
        ->set('current_password', 'wrong-current-password')
        ->set('password', $newPassword)
        ->set('password_confirmation', $newPassword)
        ->call('save')
        ->assertHasErrors('current_password');
});
