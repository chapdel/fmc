<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail;
use Spatie\Mailcoach\Domain\Content\Models\Open;

it('checks correctly that a user opened an automation mail', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasOpenedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
    ]);

    expect($condition->check())->toBeFalse();

    Open::factory()->create([
        'subscriber_id' => $subscriber->id,
        'content_item_id' => $automationMail->contentItem->id,
    ]);

    expect($condition->check())->toBeTrue();
});
