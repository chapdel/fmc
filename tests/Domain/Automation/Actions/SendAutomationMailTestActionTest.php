<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class SendAutomationMailTestActionTest extends TestCase
{
    private SendAutomationMailTestAction $action;

    private AutomationMail $automationMail;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = resolve(SendAutomationMailTestAction::class);

        /** @var AutomationMail $automationMail */
        $this->automationMail = AutomationMail::factory()->create();

        Mail::fake();
        Event::fake();
    }

    /** @test * */
    public function it_sends_a_test_mail()
    {
        $this->action->execute($this->automationMail, 'john@doe.com');

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertTrue($mail->hasTo('john@doe.com'));

            return true;
        });
    }

    /** @test * */
    public function it_sets_reply_to()
    {
        $this->automationMail->update([
            'reply_to_email' => 'foo@bar.com',
            'reply_to_name' => 'Foo',
        ]);

        $this->action->execute($this->automationMail, 'john@doe.com');

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertTrue($mail->hasReplyTo('foo@bar.com', 'Foo'));

            return true;
        });
    }
}
