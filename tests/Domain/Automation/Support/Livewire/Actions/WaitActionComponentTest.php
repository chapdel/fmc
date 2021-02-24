<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Actions;

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\WaitActionComponent;
use Spatie\Mailcoach\Tests\TestCase;

class WaitActionComponentTest extends TestCase
{
    /** @test * */
    public function it_requires_length_and_unit()
    {
        Livewire::test(WaitActionComponent::class)
            ->set('length', '')
            ->set('unit', '')
            ->call('validate')
            ->assertHasErrors([
                'length' => ['required'],
                'unit' => 'required',
            ]);
    }

    /** @test * */
    public function length_must_be_an_integer()
    {
        Livewire::test(WaitActionComponent::class)
            ->set('length', 'a')
            ->call('validate')
            ->assertHasErrors([
                'length' => 'integer',
            ]);
    }

    /** @test * */
    public function length_must_be_at_least_one()
    {
        Livewire::test(WaitActionComponent::class)
            ->set('length', 0)
            ->call('validate')
            ->assertHasErrors([
                'length' => 'min',
            ]);

        Livewire::test(WaitActionComponent::class)
            ->set('length', 1)
            ->call('validate')
            ->assertHasNoErrors();
    }

    /** @test * */
    public function unit_must_be_valid()
    {
        Livewire::test(WaitActionComponent::class)
            ->set('unit', 'something-invalid')
            ->call('validate')
            ->assertHasErrors([
                'unit' => 'in',
            ]);
    }

    /** @test * */
    public function it_emits_correct_data()
    {
        Livewire::test(WaitActionComponent::class)
            ->set('length', '5')
            ->set('unit', 'days')
            ->assertEmitted('actionUpdated', [
                'length' => 5,
                'unit' => 'days',
            ]);
    }
}
