<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Enums\WaitUnit;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Livewire\Automations\Actions\WaitActionComponent;
use Spatie\TestTime\TestTime;

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
        'builderName' => 'default',
        'action' => test()->action,
        'uuid' => $uuid,
    ])->set('length', '5')
        ->set('unit', 'days')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('actionSaved.default', $uuid, [
            'seconds' => 432000,
            'unit' => 'days',
            'length' => '5',
        ]);
});

it('can use a weekday unit', function () {
    TestTime::freeze(Carbon::make('2023-09-13'));

    $uuid = Str::uuid()->toString();

    Livewire::test(WaitActionComponent::class, [
        'builderName' => 'default',
        'action' => test()->action,
        'uuid' => $uuid,
    ])->set('length', '5')
        ->set('unit', WaitUnit::Weekdays->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('actionSaved.default', $uuid, [
            'seconds' => 604800,
            'unit' => 'weekdays',
            'length' => '5',
        ]);
});
