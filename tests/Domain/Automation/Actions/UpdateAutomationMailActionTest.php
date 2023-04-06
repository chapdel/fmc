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
        'last_modified_at' => now()->startOfSecond(),
    ]);

    test()->assertEquals([
        'name' => 'My Automation mail',
        'subject' => 'A subject',
        'html' => '<html></html>',
        'utm_tags' => false,
        'last_modified_at' => now()->startOfSecond(),
    ], [
        'name' => $automationMail->name,
        'subject' => $automationMail->subject,
        'html' => $automationMail->html,
        'utm_tags' => $automationMail->utm_tags,
        'last_modified_at' => $automationMail->last_modified_at,
    ]);
});
