<?php

use Spatie\Mailcoach\Http\App\Controllers\Settings\Account\AccountController;

it('can update the properties of the authenticated user', function () {
    $this->authenticate();

    $newName = 'New name';
    $newEmail = 'new@example.com';

    $this
        ->put(action([AccountController::class, 'update']), [
            'name' => $newName,
            'email' => $newEmail,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(action([AccountController::class, 'index']));

    $this->assertEquals($newName, auth()->user()->name);
    $this->assertEquals($newEmail, auth()->user()->email);
});
