<?php

namespace Spatie\Mailcoach\Tests\Rules;

use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Rules\EmailListSubscriptionRule;
use Spatie\Mailcoach\Tests\TestCase;

class EmailRuleSubscriptionTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Models\EmailList */
    private EmailList $emailList;

    /** @var \Spatie\Mailcoach\Rules\EmailListSubscriptionRule */
    private EmailListSubscriptionRule $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = factory(EmailList::class)->create();

        $this->rule = new EmailListSubscriptionRule($this->emailList);
    }

    /** @test */
    public function it_will_not_pass_if_the_given_email_is_already_subscribed()
    {
        $this->assertTrue($this->rule->passes('email', 'john@example.com'));
        $this->emailList->subscribe('john@example.com');
        $this->assertFalse($this->rule->passes('email', 'john@example.com'));

        $otherEmailList = factory(EmailList::class)->create();
        $rule = new EmailListSubscriptionRule($otherEmailList);
        $this->assertTrue($rule->passes('email', 'john@example.com'));
    }

    /** @test */
    public function it_will_pass_for_emails_that_are_still_pending()
    {
        $this->emailList->update(['requires_confirmation' => true]);
        $this->emailList->subscribe('john@example.com');
        $this->assertEquals(SubscriptionStatus::UNCONFIRMED, $this->emailList->getSubscriptionStatus('john@example.com'));

        $this->assertTrue($this->rule->passes('email', 'john@example.com'));
    }

    /** @test */
    public function it_will_pass_for_emails_that_are_unsubscribed()
    {
        $this->emailList->update(['requires_confirmation' => true]);
        $this->emailList->subscribe('john@example.com');
        $this->emailList->unsubscribe('john@example.com');
        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $this->emailList->getSubscriptionStatus('john@example.com'));

        $this->assertTrue($this->rule->passes('email', 'john@example.com'));
    }

    /** @test */
    public function it_will_allow_to_subscribe_an_email_that_is_already_subscribed_to_another_list()
    {
        $this->emailList->subscribe('john@example.com');

        $anotherEmailList = factory(EmailList::class)->create();

        $this->assertTrue((new EmailListSubscriptionRule($anotherEmailList))->passes('email', 'john@example.com'));
    }
}
