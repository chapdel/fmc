<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\RemoveTagsAction;
use Spatie\Mailcoach\Livewire\Automations\Actions\RemoveTagsActionComponent;

beforeEach(function () {
    test()->action = [
        'class' => RemoveTagsAction::class,
    ];
});

it('requires tags', function () {
    Livewire::test(RemoveTagsActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('tags', '')
        ->call('save')
        ->assertHasErrors([
            'tags' => ['required'],
        ]);
});

it('emits correct data', function () {
    $uuid = Str::uuid()->toString();

    Livewire::test(RemoveTagsActionComponent::class, [
        'builderName' => 'default',
        'action' => test()->action,
        'uuid' => $uuid,
    ])->set('tags', 'some,tags')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('actionSaved.default', $uuid, [
            'tags' => 'some,tags',
        ]);
});
