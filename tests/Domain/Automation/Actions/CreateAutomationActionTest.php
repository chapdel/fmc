<?php

use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('creates an automation', function () {
    $action = resolve(CreateAutomationAction::class);

    $action->execute([
        'name' => 'Some automation',
    ]);

    test()->assertEquals(1, Automation::count());
});
