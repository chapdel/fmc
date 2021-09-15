<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\ConditionActionComponent;
use Spatie\Mailcoach\Tests\TestClasses\CustomCondition;

beforeEach(function () {
    test()->action = [
        'class' => ConditionAction::class,
    ];

    test()->automation = Automation::create();
});

it('validates', function () {
    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])
        ->set('length', '')
        ->set('unit', '')
        ->call('save')
        ->assertHasErrors([
          'length' => 'required',
          'unit' => 'required',
          'condition' => 'required',
          'conditionData' => 'required',
        ]);
});

it('validates on rules of conditions', function () {
    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])
        ->set('length', 1)
        ->set('unit', 'days')
        ->set('condition', HasTagCondition::class)
        ->set('conditionData.tag', '')
        ->call('save')
        ->assertHasErrors([
            'conditionData.tag' => 'required',
        ]);
});

it('shows custom conditions', function () {
    config()->set('mailcoach.automation.flows.conditions', [
        CustomCondition::class,
    ]);

    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
        'editing' => true,
    ])
        ->assertSee('A custom condition')
        ->set('editing', false)
        ->set('condition', CustomCondition::class)
        ->assertSee('Some description');
});

test('length must be an integer', function () {
    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('length', 'a')
        ->call('save')
        ->assertHasErrors([
            'length' => 'integer',
        ]);
});

test('length must be at least one', function () {
    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('length', 0)
        ->call('save')
        ->assertHasErrors([
            'length' => 'min',
        ]);
});

test('unit must be valid', function () {
    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => Str::uuid()->toString(),
    ])->set('unit', 'something-invalid')
        ->call('save')
        ->assertHasErrors([
            'unit' => 'in',
        ]);
});

it('emits an event', function () {
    $uuid = Str::uuid()->toString();

    Livewire::test(ConditionActionComponent::class, [
        'automation' => test()->automation,
        'action' => test()->action,
        'uuid' => $uuid,
    ])  ->set('length', '5')
        ->set('unit', 'days')
        ->set('condition', HasTagCondition::class)
        ->set('conditionData.tag', 'some-tag')
        ->call('save')
        ->assertHasNoErrors()
        ->assertEmitted('actionSaved', function ($event, $params) use ($uuid) {
            expect($params[0])->toBe($uuid);
            test()->assertSame([
                'length' => 5,
                'unit' => 'days',
                'condition' => HasTagCondition::class,
                'conditionData' => [
                    'tag' => 'some-tag',
                ],
                'yesActions' => [],
                'noActions' => [],
            ], $params[1]);

            return true;
        });
});
