<?php

namespace Spatie\Mailcoach\Tests\Domain\AutomationMail\Jobs;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\AutomationMailFactory;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomAutomationMailReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithBodyReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithNoSubject;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithSubjectReplacer;
use Spatie\Snapshots\MatchesSnapshots;

class SendAutomationMailToSubscriberJobTest extends TestCase
{
    use MatchesSnapshots;

    protected AutomationMail $automationMail;

    protected Subscriber $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->automationMail = (new AutomationMailFactory())
            ->create();

        $emailList = EmailList::factory()->create();

        $this->subscriber = $emailList->subscribe('john@doe.com');
    }

    /** @test */
    public function it_can_send_a_automationMail_with_the_correct_mailer()
    {
        config()->set('mailcoach.automation.mailer', 'some-mailer');

        Event::fake();
        Mail::fake();

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));

        Mail::assertSent(MailcoachMail::class, 1);
        Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'some-mailer');

        Event::assertDispatched(AutomationMailSentEvent::class, function (AutomationMailSentEvent $event) {
            $this->assertEquals($this->automationMail->id, $event->send->automationMail->id);

            return true;
        });
    }

    /** @test */
    public function it_will_not_create_mailcoach_sends_if_they_already_have_been_created()
    {
        Event::fake();
        Mail::fake();

        SendFactory::new()->create([
            'subscriber_id' => $this->subscriber->id,
            'automation_mail_id' => $this->automationMail->id,
        ]);

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));

        $this->assertCount(1, Send::all());
    }

    /** @test */
    public function it_will_use_the_right_subject()
    {
        Event::fake();
        Mail::fake();

        $this->automationMail->subject('my subject');

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $automationMailMail) {
            $this->assertEquals('my subject', $automationMailMail->subject);

            return true;
        });
    }

    /** @test */
    public function it_will_use_the_reply_to_fields()
    {
        Event::fake();
        Mail::fake();

        $this->automationMail->replyTo('replyto@example.com', 'Reply to John Doe');

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $automationMailMail) {
            return $automationMailMail->build()->hasReplyTo('replyto@example.com', 'Reply to John Doe');
        });
    }

    /** @test */
    public function it_will_prepare_the_webview()
    {
        Event::fake();
        Mail::fake();

        $this->automationMail->update([
            'html' => 'my html',
            'webview_html' => null,
        ]);

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));

        $this->assertMatchesHtmlSnapshot($this->automationMail->refresh()->webview_html);
    }

    /** @test */
    public function it_will_not_send_invalid_html()
    {
        Event::fake();
        Mail::fake();

        $this->automationMail->update([
            'track_clicks' => true,
            'html' => '<qsdfqlsmdkjm><<>><<',
        ]);

        $this->expectException(CouldNotSendAutomationMail::class);

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));
    }

    /** @test */
    public function the_queue_of_the_send_automationMail_job_can_be_configured()
    {
        Event::fake();
        Mail::fake();

        Queue::fake();

        config()->set('mailcoach.automation.perform_on_queue.send_automation_mail_to_subscriber_job', 'custom-queue');

        $automationMail = AutomationMail::factory()->create();
        dispatch(new SendAutomationMailToSubscriberJob($automationMail, $this->subscriber));

        Queue::assertPushedOn('custom-queue', SendAutomationMailToSubscriberJob::class);
    }

    /** @test */
    public function personalized_placeholders_in_the_subject_will_be_replaced()
    {
        Mail::fake();

        $this->automationMail->update([
            'subject' => 'This is a mail sent to ::subscriber.email::',
        ]);

        dispatch(new SendAutomationMailToSubscriberJob($this->automationMail, $this->subscriber));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertEquals("This is a mail sent to {$this->subscriber->email}", $mail->subject);

            return true;
        });
    }

    /** @test * */
    public function custom_mailable_sends()
    {
        $this->automationMail->useMailable(TestMailcoachMail::class);

        config()->set('mailcoach.automation.mailer', 'array');

        $this->automationMail->send($this->subscriber);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "This is the subject from the custom mailable.";
        })->count() > 0);
    }

    /** @test * */
    public function custom_mailable_subject_overrides_automationMail_subject()
    {
        $this->automationMail->useMailable(TestMailcoachMail::class);
        $this->automationMail->update([
            'subject' => 'This subject comes from the automationMail',
        ]);
        config()->set('mailcoach.automation.mailer', 'array');

        $this->automationMail->send($this->subscriber);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "This is the subject from the custom mailable.";
        })->count() > 0);
    }

    /** @test * */
    public function custom_replacers_work_with_automationMail_subject()
    {
        $this->automationMail->update([
            'subject' => '::customreplacer::',
            'email_html' => '::customreplacer::',
        ]);
        $this->automationMail->useMailable(TestMailcoachMailWithNoSubject::class);

        config()->set('mailcoach.automation.replacers', array_merge(config('mailcoach.automation.replacers'), [CustomAutomationMailReplacer::class]));
        config()->set('mailcoach.automation.mailer', 'array');

        $this->automationMail->send($this->subscriber);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "The custom replacer works";
        })->count() > 0);
    }

    /** @test * */
    public function custom_replacers_work_with_subject_from_custom_mailable()
    {
        $this->automationMail->useMailable(TestMailcoachMailWithSubjectReplacer::class);

        config()->set('mailcoach.automation.replacers', array_merge(config('mailcoach.automation.replacers'), [CustomAutomationMailReplacer::class]));
        config()->set('mailcoach.automation.mailer', 'array');

        $this->automationMail->send($this->subscriber);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "Custom Subject: The custom replacer works";
        })->count() > 0);
    }

    /** @test * */
    public function custom_replacers_work_in_body_from_custom_mailable()
    {
        $this->automationMail->useMailable(TestMailcoachMailWithBodyReplacer::class);

        config()->set('mailcoach.automation.replacers', array_merge(config('mailcoach.automation.replacers'), [CustomAutomationMailReplacer::class]));
        config()->set('mailcoach.automation.mailer', 'array');

        $this->automationMail->send($this->subscriber);

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return Str::contains($message->getBody(), 'The custom replacer works');
        })->count() > 0);
    }
}
