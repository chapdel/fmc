<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Tests\TestCase;

class HasClickedAutomationMailTest extends TestCase
{
    /** @test * */
    public function it_checks_correctly_that_a_user_clicked_an_automation_mail()
    {
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasClickedAutomationMail($subscriber, [
            'automation_mail_id' => $automationMail->id,
            'automation_mail_link_url' => 'https://spatie.be',
        ]);

        $this->assertFalse($condition->check());

        $click = AutomationMailClick::factory()->create([
            'subscriber_id' => $subscriber->id,
            'url' => 'https://spatie.be',
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertTrue($condition->check());
    }

    /** @test * */
    public function it_returns_false_if_a_link_is_specified_and_its_not_the_link()
    {
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasClickedAutomationMail($subscriber, [
            'automation_mail_id' => $automationMail->id,
            'automation_mail_link_url' => 'https://example.com',
        ]);

        $this->assertFalse($condition->check());

        $click = AutomationMailClick::factory()->create([
            'subscriber_id' => $subscriber->id,
            'url' => 'https://spatie.be',
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertFalse($condition->check());
    }

    /** @test * */
    public function it_returns_true_if_a_link_isnt_specified_and_any_link_was_clicked()
    {
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasClickedAutomationMail($subscriber, [
            'automation_mail_id' => $automationMail->id,
        ]);

        $this->assertFalse($condition->check());

        $click = AutomationMailClick::factory()->create([
            'subscriber_id' => $subscriber->id,
            'url' => 'https://spatie.be',
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertTrue($condition->check());
    }
}
