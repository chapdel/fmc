<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;

beforeEach(function () {
    test()->action = [
        'class' => WaitAction::class,
    ];
});

it('requires length and unit', function () {
    Livewire::test(WaitActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('length', '')
        ->set('unit', '')
        ->call('save')
        ->assertHasErrors([
            'length' => ['required'],
            'unit' => 'required',
        ]);
});

test('length must be an integer', function () {
    Livewire::test(WaitActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('length', 'a')
        ->call('save')
        ->assertHasErrors([
            'length' => 'integer',
        ]);
});

test('length must be at least one', function () {
    Livewire::test(WaitActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('length', 0)
        ->call('save')
        ->assertHasErrors([
            'length' => 'min',
        ]);

    Livewire::test(WaitActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('length', 1)
        ->call('save')
        ->assertHasNoErrors();
});

test('unit must be valid', function () {
    Livewire::test(WaitActionComponent::class, [
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('unit', 'something-invalid')
        ->call('save')
        ->assertHasErrors([
            'unit' => 'in',
        ]);
});

it('emits correct data', function () {
    $uuid = Str::uuid()->toString();

    Livewire::test(WaitActionComponent::class, [
        'action' => test()->action,
        'uuid' => $uuid,
    ])->set('length', '5')
        ->set('unit', 'days')
        ->call('save')
        ->assertHasNoErrors()
        ->assertEmitted('actionSaved', $uuid, [
            'seconds' => 432000,
            'unit' => 'days',
            'length' => '5',
        ]);
});
