<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomWelcomeMail;

class WelcomeMailTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create([
            'name' => 'my newsletter',
            'requires_confirmation' => false,
            'send_welcome_mail' => true,
            'transactional_mailer' => 'some-transactional-mailer',
        ]);
    }

    /** @test */
    public function it_will_send_a_welcome_mail_when_a_subscriber_has_subscribed_with_the_correct_mailer()
    {
        Mail::fake();

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
            $this->assertEquals('some-transactional-mailer', $mail->mailer);

            return true;
        });
    }

    /** @test */
    public function it_will_not_send_a_welcome_mail_if_it_is_not_enabled_on_the_email_list()
    {
        Mail::fake();

        $this->emailList->update(['send_welcome_mail' => false]);

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_will_send_a_welcome_mail_when_a_subscribed_gets_confirmed()
    {
        Mail::fake();

        $this->emailList->update(['requires_confirmation' => true]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertNotQueued(WelcomeMail::class);

        $subscriber->confirm();

        Mail::assertQueued(WelcomeMail::class);
    }

    /** @test */
    public function the_welcome_mail_has_a_default_subject()
    {
        Mail::fake();

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
            $mail->build();

            $this->assertStringContainsString('Welcome', $mail->subject);

            return true;
        });
    }

    /** @test */
    public function the_subject_of_the_welcome_mail_can_be_customized()
    {
        Mail::fake();

        $this->emailList->update(['welcome_mail_subject' => 'Hello ::subscriber.first_name::, welcome to ::list.name::']);

        Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo($this->emailList);

        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) {
            $mail->build();
            $this->assertEquals('Hello John, welcome to my newsletter', $mail->subject);

            return true;
        });
    }

    /** @test */
    public function the_welcome_mail_has_default_content()
    {
        $this->emailList->update(['transactional_mailer' => 'log']);

        $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo($this->emailList);

        $content = (new WelcomeMail($subscriber))->render();

        $this->assertStringContainsString('You are now subscribed', $content);
    }

    /** @test */
    public function the_welcome_mail_can_have_custom_content()
    {
        $this->emailList->update(['transactional_mailer' => 'log']);

        Subscriber::$fakeUuid = 'my-uuid';

        $this->emailList->update(['welcome_mail_content' => 'Hi ::subscriber.first_name::, welcome to ::list.name::. Here is a link to unsubscribe ::unsubscribeUrl::']);

        $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo($this->emailList);

        $content = (new WelcomeMail($subscriber))->render();

        $this->assertStringContainsString('Hi John, welcome to my newsletter. Here is a link to unsubscribe http://localhost/mailcoach/unsubscribe/my-uuid', $content);
    }

    /** @test */
    public function it_can_use_custom_welcome_mailable()
    {
        Mail::fake();

        $this->emailList->update(['welcome_mailable_class' => CustomWelcomeMail::class]);

        Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);

        Mail::assertQueued(CustomWelcomeMail::class);
    }
}
