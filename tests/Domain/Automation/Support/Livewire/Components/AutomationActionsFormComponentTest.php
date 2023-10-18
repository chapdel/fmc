<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Livewire\Automations\AutomationActionsComponent;

it('listens to edit and saved events to disable the save button', function () {
    /** @var Automation $automation */
    $automation = Automation::factory()->create();
    $automation->chain([
        new UnsubscribeAction(),
    ]);

    $someActionUuid = Str::uuid()->toString();

    Livewire::test(AutomationActionsComponent::class, [
        'automation' => $automation,
    ])->assertDontSee('disabled')
        ->dispatch('editAction.default', $someActionUuid)
        ->assertSee('disabled')
        ->dispatch('actionSaved.default', $someActionUuid, [])
        ->assertDontSee('disabled')
        ->dispatch('editAction.default', $someActionUuid)
        ->assertSee('disabled')
        ->dispatch('actionDeleted.default', $someActionUuid)
        ->assertDontSee('disabled');
});

it('puts actions from the automation builder in the form', function () {
    /** @var Automation $automation */
    $automation = Automation::factory()->create();
    $automation->chain([
        new UnsubscribeAction(),
    ]);

    Livewire::test(AutomationActionsComponent::class, [
        'automation' => $automation,
    ])->assertSee($automation->actions->first()->toLivewireArray()['uuid']);
});

it('updates actions when the default builder is updated', function () {
    /** @var Automation $automation */
    $automation = Automation::factory()->create();
    $automation->chain([
        new UnsubscribeAction(),
    ]);

    Livewire::test(AutomationActionsComponent::class, [
        'automation' => $automation,
    ])->assertSee($automation->actions->first()->toLivewireArray()['uuid']
    )->dispatch('automationBuilderUpdated.default', [
        'actions' => [
            [
                'uuid' => '486b38a0-1421-43c9-ab3f-debd0e959650',
                'class' => WaitAction::class,
                'data' => [
                    'length' => '1',
                    'unit' => 'days',
                ],
                'active' => 0,
                'completed' => 0,
            ],
        ],
    ])->assertSet('actions', [
        [
            'uuid' => '486b38a0-1421-43c9-ab3f-debd0e959650',
            'class' => WaitAction::class,
            'data' => [
                'length' => '1',
                'unit' => 'days',
            ],
            'active' => 0,
            'completed' => 0,
        ],
    ]);
});
