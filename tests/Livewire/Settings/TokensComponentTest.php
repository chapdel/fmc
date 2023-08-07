<?php

use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->authenticate();
});

it('can delete a personal access token', function () {
    $token = auth()->user()->personalAccessTokens()->first();

    \Livewire\Livewire::test('mailcoach::tokens')
        ->call('delete', $token->id);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->id,
    ]);
});

it('will not delete a personal access token belonging to another user', function () {
    $this->withExceptionHandling();

    $anotherUser = User::factory()->create();

    $anotherUser->createToken('test');

    $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

    \Livewire\Livewire::test('mailcoach::tokens')
        ->call('delete', $anotherUser->personalAccessTokens()->first()->id)
        ->assertForbidden();
});
