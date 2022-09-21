<?php

use Spatie\Mailcoach\Http\Api\Controllers\UserController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

it('can detect the currently logged in user', function () {
    test()->loginToApi();

    $this
        ->getJson(action(UserController::class))
        ->assertSuccessful()
        ->assertJsonFragment([
            'email' => auth()->user()->email,
        ]);
});
