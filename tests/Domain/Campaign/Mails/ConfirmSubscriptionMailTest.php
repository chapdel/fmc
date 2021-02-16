<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomConfirmSubscriberMail;

class ConfirmSubscriptionMailTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create([
            'requires_confirmation' => true,
            'name' => 'my newsletter',
            'transactional_mailer' => 'some-transactional-mailer',
        ]);
    }

    /** @test */
    public function the_confirmation_mail_is_sent_with_the_correct_mailer()
    {
        Mail::fake();

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
            $this->assertEquals('some-transactional-mailer', $mail->mailer);

            return true;
        });
    }

    /** @test */
    public function the_confirmation_mail_has_a_default_subject()
    {
        Mail::fake();

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
            $mail->build();

            $this->assertStringContainsString('Confirm', $mail->subject);

            return true;
        });
    }

    /** @test */
    public function the_subject_of_the_confirmation_mail_can_be_customized()
    {
        Mail::fake();

        $this->emailList->update(['confirmation_mail_subject' => 'Hello ::subscriber.first_name::, welcome to ::list.name::']);

        Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo($this->emailList);

        Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) {
            $mail->build();
            $this->assertEquals('Hello John, welcome to my newsletter', $mail->subject);

            return true;
        });
    }

    /** @test */
    public function the_confirmation_mail_has_default_content()
    {
        $this->emailList->update(['transactional_mailer' => 'log']);

        $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo($this->emailList);

        $content = (new ConfirmSubscriberMail($subscriber))->render();

        $this->assertStringContainsString('confirm', $content);
    }

    /** @test */
    public function the_confirmation_mail_can_have_custom_content()
    {
        $this->emailList->update(['transactional_mailer' => 'log']);

        Subscriber::$fakeUuid = 'my-uuid';

        $this->emailList->update(['confirmation_mail_content' => 'Hi ::subscriber.first_name::, press ::confirmUrl:: to subscribe to ::list.name::']);

        $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo($this->emailList);

        $content = (new ConfirmSubscriberMail($subscriber))->render();

        $this->assertStringContainsString('Hi John, press http://localhost/mailcoach/confirm-subscription/my-uuid to subscribe to my newsletter', $content);
    }

    /** @test */
    public function it_can_use_custom_welcome_mailable()
    {
        Mail::fake();

        $this->emailList->update(['confirmation_mailable_class' => CustomConfirmSubscriberMail::class]);

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertQueued(CustomConfirmSubscriberMail::class);
    }
}
