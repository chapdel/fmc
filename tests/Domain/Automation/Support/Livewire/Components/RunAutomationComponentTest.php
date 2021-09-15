<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\UnsubscribeAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can start and pause an automation', function () {
    /** @var Automation $automation */
    $automation = Automation::factory()->create();
    $automation->triggerOn(new SubscribedTrigger());
    $automation->chain([
        new UnsubscribeAction(),
    ]);

    expect($automation->fresh()->status)->toBe(AutomationStatus::PAUSED);

    Livewire::test('run-automation', ['automation' => $automation])
        ->call('start');

    expect($automation->fresh()->status)->toBe(AutomationStatus::STARTED);

    Livewire::test('run-automation', ['automation' => $automation])
        ->call('pause');

    expect($automation->fresh()->status)->toBe(AutomationStatus::PAUSED);
});
