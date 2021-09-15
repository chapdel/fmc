<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail;

it('checks correctly that a user opened an automation mail', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasOpenedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
    ]);

    expect($condition->check())->toBeFalse();

    AutomationMailOpen::factory()->create([
        'subscriber_id' => $subscriber->id,
        'automation_mail_id' => $automationMail->id,
    ]);

    expect($condition->check())->toBeTrue();
});
