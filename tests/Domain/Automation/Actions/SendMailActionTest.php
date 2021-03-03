<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Swift_Message;

class SendMailActionTest extends TestCase
{
    private SendMailAction $action;

    private Send $send;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = resolve(SendMailAction::class);

        /** @var Send $send */
        $this->send = Send::factory()->create(['campaign_id' => null]);

        Mail::fake();
        Event::fake();
    }

    /** @test * */
    public function it_sends_a_pending_send()
    {
        $this->action->execute($this->send);

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertTrue($mail->hasTo($this->send->subscriber->email));

            return true;
        });

        Event::assertDispatched(AutomationMailSentEvent::class);

        $this->assertTrue($this->send->wasAlreadySent());
    }

    /** @test * */
    public function it_sets_reply_to()
    {
        $this->send->automationMail->update([
            'reply_to_email' => 'foo@bar.com',
            'reply_to_name' => 'Foo',
        ]);

        $this->action->execute($this->send);

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertTrue($mail->hasReplyTo('foo@bar.com', 'Foo'));

            return true;
        });
    }

    /** @test * */
    public function it_wont_send_again_if_the_send_was_already_sent()
    {
        $this->action->execute($this->send);
        $this->action->execute($this->send);

        Mail::assertSent(MailcoachMail::class, 1);
        Event::assertDispatched(AutomationMailSentEvent::class, 1);
    }
}
