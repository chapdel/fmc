<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Livewire\Components;

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Tests\TestCase;

class AutomationActionsFormComponentTest extends TestCase
{
    /** @test * */
    public function it_listens_to_edit_and_saved_events_to_disable_the_save_button()
    {
        /** @var Automation $automation */
        $automation = Automation::factory()->create();
        $automation->chain([
            new UnsubscribeAction(),
        ]);

        $someActionUuid = Str::uuid()->toString();

        Livewire::test('automation-actions', [
            'automation' => $automation,
        ])->assertDontSee('disabled')
            ->emit('editAction', $someActionUuid)
            ->assertSee('disabled')
            ->emit('actionSaved', $someActionUuid, [])
            ->assertDontSee('disabled')
            ->emit('editAction', $someActionUuid)
            ->assertSee('disabled')
            ->emit('actionDeleted', $someActionUuid)
            ->assertDontSee('disabled');
    }

    /** @test * */
    public function it_puts_actions_from_the_automation_builder_in_the_form()
    {
        /** @var Automation $automation */
        $automation = Automation::factory()->create();
        $automation->chain([
            new UnsubscribeAction(),
        ]);

        Livewire::test('automation-actions', [
            'automation' => $automation,
        ])->assertSee(json_encode([
            $automation->actions->first()->toLivewireArray(),
        ]));
    }

    /** @test * */
    public function it_updates_actions_when_the_builder_is_updated()
    {
        /** @var Automation $automation */
        $automation = Automation::factory()->create();
        $automation->chain([
            new UnsubscribeAction(),
        ]);

        Livewire::test('automation-actions', [
            'automation' => $automation,
        ])->assertSee(json_encode([
            $automation->actions->first()->toLivewireArray(),
        ]))->emit('automationBuilderUpdated', [
            'actions' => [
                [
                    "uuid" => "486b38a0-1421-43c9-ab3f-debd0e959650",
                    "class" => WaitAction::class,
                    "data" => [
                      "length" => "1",
                      "unit" => "days",
                    ],
                    "active" => 0,
                    "completed" => 0,
                ]
            ],
        ])->assertViewHas('actions', [
            [
                "uuid" => "486b38a0-1421-43c9-ab3f-debd0e959650",
                "class" => WaitAction::class,
                "data" => [
                    "length" => "1",
                    "unit" => "days",
                ],
                "active" => 0,
                "completed" => 0,
            ]
        ]);
    }
}
