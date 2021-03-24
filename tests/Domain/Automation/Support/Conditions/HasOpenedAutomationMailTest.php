<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail;
use Spatie\Mailcoach\Tests\TestCase;

class HasOpenedAutomationMailTest extends TestCase
{
    /** @test * */
    public function it_checks_correctly_that_a_user_opened_an_automation_mail()
    {
        $automation = Automation::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasOpenedAutomationMail($automation, $subscriber, [
            'automation_mail_id' => $automationMail->id,
        ]);

        $this->assertFalse($condition->check());

        AutomationMailOpen::factory()->create([
            'subscriber_id' => $subscriber->id,
            'automation_mail_id' => $automationMail->id,
        ]);

        $this->assertTrue($condition->check());
    }
}
