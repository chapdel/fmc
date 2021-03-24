<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;
use Spatie\Mailcoach\Tests\TestCase;

class WaitActionComponentTest extends TestCase
{
    private array $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = [
            'class' => WaitAction::class,
        ];
    }

    /** @test * */
    public function it_requires_length_and_unit()
    {
        Livewire::test(WaitActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('length', '')
          ->set('unit', '')
          ->call('save')
          ->assertHasErrors([
            'length' => ['required'],
            'unit' => 'required',
          ]);
    }

    /** @test * */
    public function length_must_be_an_integer()
    {
        Livewire::test(WaitActionComponent::class, [
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
        Livewire::test(WaitActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('length', 0)
            ->call('save')
            ->assertHasErrors([
                'length' => 'min',
            ]);

        Livewire::test(WaitActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('length', 1)
            ->call('save')
            ->assertHasNoErrors();
    }

    /** @test * */
    public function unit_must_be_valid()
    {
        Livewire::test(WaitActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('unit', 'something-invalid')
            ->call('save')
            ->assertHasErrors([
                'unit' => 'in',
            ]);
    }

    /** @test * */
    public function it_emits_correct_data()
    {
        $uuid = Str::uuid()->toString();

        Livewire::test(WaitActionComponent::class, [
            'action' => $this->action,
            'uuid' => $uuid,
        ])  ->set('length', '5')
            ->set('unit', 'days')
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted('actionSaved', $uuid, [
                'length' => 5,
                'unit' => 'days',
            ]);
    }
}
