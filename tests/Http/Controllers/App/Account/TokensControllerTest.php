<?php

use Spatie\Mailcoach\Domain\Settings\Models\User;

beforeEach(function () {
    $this->authenticate();
});

it('can delete a personal access token', function () {
    $token = auth()->user()->personalAccessTokens()->first();

    $this
        ->delete(route('tokens.delete', $token->id))
        ->assertRedirect(route('tokens'));

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->id,
    ]);
});

it('will not delete a personal access token belonging to another user', function () {
    $this->withExceptionHandling();

    $anotherUser = User::factory()->create();

    $anotherUser->createToken('test');

    $this
        ->delete(route('tokens.delete', $anotherUser->personalAccessTokens()->first()))
        ->assertForbidden();
});
