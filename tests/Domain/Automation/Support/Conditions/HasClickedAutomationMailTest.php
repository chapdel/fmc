<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Tests\TestCase;

class HasClickedAutomationMailTest extends TestCase
{
    /** @test * */
    public function it_checks_correctly_that_a_user_clicked_an_automation_mail()
    {
        $automation = Automation::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasClickedAutomationMail($automation, $subscriber, [
            'automation_mail_id' => $automationMail->id,
            'automation_mail_link_url' => 'https://spatie.be',
        ]);

        $this->assertFalse($condition->check());

        $link = AutomationMailLink::factory()->create([
            'url' => 'https://spatie.be',
        ]);
        $click = AutomationMailClick::factory()->create([
            'automation_mail_link_id' => $link->id,
            'subscriber_id' => $subscriber->id,
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertTrue($condition->check());
    }

    /** @test * */
    public function it_checks_correctly_that_a_user_clicked_an_automation_mail_with_utm_tags()
    {
        $automation = Automation::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create([
            'utm_tags' => true,
        ]);

        $condition = new HasClickedAutomationMail($automation, $subscriber, [
            'automation_mail_id' => $automationMail->id,
            'automation_mail_link_url' => 'https://spatie.be',
        ]);

        $this->assertFalse($condition->check());

        $link = AutomationMailLink::factory()->create([
            'url' => app(AddUtmTagsToUrlAction::class)->execute('https://spatie.be', $automationMail->name),
        ]);
        $click = AutomationMailClick::factory()->create([
            'automation_mail_link_id' => $link->id,
            'subscriber_id' => $subscriber->id,
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertTrue($condition->check());
    }

    /** @test * */
    public function it_returns_false_if_a_link_is_specified_and_its_not_the_link()
    {
        $automation = Automation::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasClickedAutomationMail($automation, $subscriber, [
            'automation_mail_id' => $automationMail->id,
            'automation_mail_link_url' => 'https://example.com',
        ]);

        $this->assertFalse($condition->check());

        $link = AutomationMailLink::factory()->create([
            'url' => 'https://spatie.be',
        ]);
        $click = AutomationMailClick::factory()->create([
            'automation_mail_link_id' => $link->id,
            'subscriber_id' => $subscriber->id,
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertFalse($condition->check());
    }

    /** @test * */
    public function it_returns_true_if_a_link_isnt_specified_and_any_link_was_clicked()
    {
        $automation = Automation::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $automationMail = AutomationMail::factory()->create();

        $condition = new HasClickedAutomationMail($automation, $subscriber, [
            'automation_mail_id' => $automationMail->id,
        ]);

        $this->assertFalse($condition->check());

        $link = AutomationMailLink::factory()->create([
            'url' => 'https://spatie.be',
        ]);
        $click = AutomationMailClick::factory()->create([
            'automation_mail_link_id' => $link->id,
            'subscriber_id' => $subscriber->id,
        ]);
        $click->send->update(['automation_mail_id' => $automationMail->id]);

        $this->assertTrue($condition->check());
    }
}
