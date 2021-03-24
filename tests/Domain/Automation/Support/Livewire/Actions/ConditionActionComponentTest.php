<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\ConditionAction;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\ConditionActionComponent;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomCondition;

class ConditionActionComponentTest extends TestCase
{
    private Automation $automation;

    private array $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = [
            'class' => ConditionAction::class,
        ];

        $this->automation = Automation::create();
    }

    /** @test * */
    public function it_validates()
    {
        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
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
    }

    /** @test * */
    public function it_validates_on_rules_of_conditions()
    {
        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
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
    }

    /** @test * */
    public function it_shows_custom_conditions()
    {
        config()->set('mailcoach.automation.flows.conditions', [
            CustomCondition::class,
        ]);

        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
            'editing' => true,
        ])
            ->assertSee('A custom condition')
            ->set('editing', false)
            ->set('condition', CustomCondition::class)
            ->assertSee('Some description');
    }

    /** @test * */
    public function length_must_be_an_integer()
    {
        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('length', 'a')
            ->call('save')
            ->assertHasErrors([
                'length' => 'integer',
            ]);
    }

    /** @test * */
    public function length_must_be_at_least_one()
    {
        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('length', 0)
            ->call('save')
            ->assertHasErrors([
                'length' => 'min',
            ]);
    }

    /** @test * */
    public function unit_must_be_valid()
    {
        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('unit', 'something-invalid')
            ->call('save')
            ->assertHasErrors([
                'unit' => 'in',
            ]);
    }

    /** @test * */
    public function it_emits_an_event()
    {
        $uuid = Str::uuid()->toString();

        Livewire::test(ConditionActionComponent::class, [
            'automation' => $this->automation,
            'action' => $this->action,
            'uuid' => $uuid,
        ])  ->set('length', '5')
            ->set('unit', 'days')
            ->set('condition', HasTagCondition::class)
            ->set('conditionData.tag', 'some-tag')
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted('actionSaved', function ($event, $params) use ($uuid) {
                $this->assertSame($uuid, $params[0]);
                $this->assertSame([
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
    }
}
