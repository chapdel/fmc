<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AddTagsAction;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions\AddTagsActionComponent;
use Spatie\Mailcoach\Tests\TestCase;

class AddTagsActionComponentTest extends TestCase
{
    private array $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = [
            'class' => AddTagsAction::class,
        ];
    }

    /** @test * */
    public function it_requires_tags()
    {
        Livewire::test(AddTagsActionComponent::class, [
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

        Livewire::test(AddTagsActionComponent::class, [
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
