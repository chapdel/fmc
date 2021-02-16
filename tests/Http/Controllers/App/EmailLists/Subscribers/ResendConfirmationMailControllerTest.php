<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists\Subscribers;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class ResendConfirmationMailControllerTest extends TestCase
{
    /** @test */
    public function it_can_resend_the_confirmation_mail_with_the_correct_mailer()
    {
        $this->authenticate();
        Mail::fake();

        $emailList = EmailList::factory()->create([
            'requires_confirmation' => true,
            'transactional_mailer' => 'some-mailer',
        ]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
        Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
            $this->assertEquals('some-mailer', $mail->mailer);

            return true;
        });

        $this->post(route('mailcoach.subscriber.resend-confirmation-mail', $subscriber));
        Mail::assertQueued(ConfirmSubscriberMail::class, 2);
    }
}
