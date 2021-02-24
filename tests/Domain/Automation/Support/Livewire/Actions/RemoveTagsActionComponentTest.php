<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\RemoveTagsAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\RemoveTagsActionComponent;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\ValidationRules\Rules\Delimited;

class RemoveTagsActionComponentTest extends TestCase
{
    private array $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = [
            'class' => RemoveTagsAction::class,
        ];
    }

    /** @test * */
    public function it_requires_tags()
    {
        Livewire::test(RemoveTagsActionComponent::class, [
            'action' => $this->action,
            'uuid' => Str::uuid()->toString(),
        ])->set('tags', '')
          ->call('save')
          ->assertHasErrors([
            'tags' => ['required'],
          ]);
    }

    /** @test * */
    public function it_emits_correct_data()
    {
        $uuid = Str::uuid()->toString();

        Livewire::test(RemoveTagsActionComponent::class, [
            'action' => $this->action,
            'uuid' => $uuid,
        ])  ->set('tags', 'some,tags')
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted('actionSaved', $uuid, [
                'tags' => 'some,tags',
            ]);
    }
}
