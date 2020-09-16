<?php

namespace Spatie\Mailcoach\Tests\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Actions\Subscribers\SendWelcomeMailAction;
use Spatie\Mailcoach\Mails\WelcomeMail;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class SendWelcomeMailActionTest extends TestCase
{
    private Subscriber $subscriber;

    public function setUp(): void
    {
        parent::setup();

        $this->subscriber = Subscriber::factory()->create();

        $this->subscriber->emailList->update([
            'send_welcome_mail' => true,
            'transactional_mailer' => 'some-mailer',
        ]);
    }

    /** @test */
    public function it_can_send_a_welcome_mail_with_the_correct_mailer()
    {
        Mail::fake();

        $action = new SendWelcomeMailAction();

        $action->execute($this->subscriber);

        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
            $this->assertEquals('some-mailer', $mail->mailer);

            return true;
        });
    }
}
