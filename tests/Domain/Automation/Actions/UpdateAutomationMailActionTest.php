<?php

use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\TestTime\TestTime;

it('updates an automation mail', function () {
    TestTime::freeze();

    $automationMail = AutomationMail::factory()->create();

    $action = resolve(UpdateAutomationMailAction::class);

    $action->execute($automationMail, [
        'name' => 'My Automation mail',
        'subject' => 'A subject',
        'html' => '<html></html>',
        'utm_tags' => false,
    ]);

    expect([
        'name' => $automationMail->name,
        'subject' => $automationMail->contentItem->subject,
        'html' => $automationMail->contentItem->html,
        'utm_tags' => $automationMail->contentItem->utm_tags,
    ])->toEqual([
        'name' => 'My Automation mail',
        'subject' => 'A subject',
        'html' => '<html></html>',
        'utm_tags' => false,
    ]);
});
