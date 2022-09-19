<?php

use Laravel\Sanctum\Sanctum;
use Spatie\Mailcoach\Domain\Settings\Models\User;
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

it('can use the api via sanctum', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['*'], 'api');

    $this
        ->getJson('mailcoach/api/user')
        ->assertSuccessful()
        ->assertJsonFragment(['email' => $user->email]);
})->skip('to update');
