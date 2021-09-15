<?php

use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;

it('listens to edit and saved events to disable the save button', function () {
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
});

it('puts actions from the automation builder in the form', function () {
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
});

it('updates actions when the default builder is updated', function () {
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
        'name' => 'default',
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
            ],
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
        ],
    ]);
});

it('doesnt update when other builders get updated', function () {
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
        'name' => 'some-other',
        'actions' => [],
    ])->assertViewHas('actions', [
        $automation->actions->first()->toLivewireArray(),
    ]);
});
