<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Jobs;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomCampaignReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithBodyReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithNoSubject;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithSubjectReplacer;
use Spatie\Snapshots\MatchesSnapshots;

class SendCampaignJobTest extends TestCase
{
    use MatchesSnapshots;

    protected Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())
            ->withSubscriberCount(3)
            ->create();

        $this->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);
    }

    /** @test */
    public function it_can_send_a_campaign_with_the_correct_mailer()
    {
        Event::fake();
        Mail::fake();

        dispatch(new SendCampaignJob($this->campaign));

        Mail::assertSent(MailcoachMail::class, 3);
        Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'some-mailer');

        Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) {
            $this->assertEquals($this->campaign->id, $event->campaign->id);

            return true;
        });

        $this->campaign->refresh();
        $this->assertEquals(CampaignStatus::SENT, $this->campaign->status);
        $this->assertEquals(3, $this->campaign->sent_to_number_of_subscribers);
    }

    /** @test */
    public function it_will_not_create_mailcoach_sends_if_they_already_have_been_created()
    {
        Event::fake();
        Mail::fake();

        $emailList = EmailList::factory()->create();

        $campaign = Campaign::factory()->create([
            'email_list_id' => $emailList->id,
        ]);

        $subscriber = Subscriber::factory()->create([
            'email_list_id' => $emailList->id,
            'subscribed_at' => now(),
        ]);

        SendFactory::new()->create([
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertCount(1, Send::all());
    }

    /** @test */
    public function it_will_use_the_right_subject()
    {
        Event::fake();
        Mail::fake();

        $this->campaign->subject('my subject');

        dispatch(new SendCampaignJob($this->campaign));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $campaignMail) {
            $this->assertEquals('my subject', $campaignMail->subject);

            return true;
        });
    }

    /** @test */
    public function it_will_use_the_reply_to_fields()
    {
        Event::fake();
        Mail::fake();

        $this->campaign->replyTo('replyto@example.com', 'Reply to John Doe');

        dispatch(new SendCampaignJob($this->campaign));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $campaignMail) {
            return $campaignMail->build()->hasReplyTo('replyto@example.com', 'Reply to John Doe');
        });
    }

    /** @test */
    public function a_campaign_that_was_sent_will_not_be_sent_again()
    {
        Event::fake();
        Mail::fake();

        $this->assertFalse($this->campaign->wasAlreadySent());
        dispatch(new SendCampaignJob($this->campaign));
        $this->assertTrue($this->campaign->refresh()->wasAlreadySent());
        Mail::assertSent(MailcoachMail::class, 3);

        dispatch(new SendCampaignJob($this->campaign));
        Mail::assertSent(MailcoachMail::class, 3);
        Event::assertDispatched(CampaignSentEvent::class, 1);
    }

    /** @test */
    public function it_will_prepare_the_webview()
    {
        Event::fake();
        Mail::fake();

        $this->campaign->update([
            'html' => 'my html',
            'webview_html' => null,
        ]);

        dispatch(new SendCampaignJob($this->campaign));

        $this->assertMatchesHtmlSnapshotWithoutWhitespace($this->campaign->refresh()->webview_html);
    }

    /** @test */
    public function it_will_not_send_invalid_html()
    {
        Event::fake();
        Mail::fake();

        $this->campaign->update([
            'track_clicks' => true,
            'html' => '<qsdfqlsmdkjm><<>><<',
        ]);

        $this->expectException(CouldNotSendCampaign::class);

        dispatch(new SendCampaignJob($this->campaign));
    }

    /** @test */
    public function the_queue_of_the_send_campaign_job_can_be_configured()
    {
        Event::fake();
        Mail::fake();

        Queue::fake();

        config()->set('mailcoach.campaigns.perform_on_queue.send_campaign_job', 'custom-queue');

        $campaign = Campaign::factory()->create();
        dispatch(new SendCampaignJob($campaign));

        Queue::assertPushedOn('custom-queue', SendCampaignJob::class);
    }

    /** @test */
    public function regular_placeholders_in_the_subject_will_be_replaced()
    {
        Mail::fake();

        $campaign = (new CampaignFactory())
            ->withSubscriberCount(1)
            ->create([
                'subject' => 'This is a mail sent to ::list.name::',
            ]);

        $campaign->emailList->update(['name' => 'my list']);

        dispatch(new SendCampaignJob($campaign));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
            $this->assertEquals("This is a mail sent to my list", $mail->subject);

            return true;
        });
    }

    /** @test */
    public function personalized_placeholders_in_the_subject_will_be_replaced()
    {
        Mail::fake();

        $campaign = (new CampaignFactory())
            ->create([
                'subject' => 'This is a mail sent to ::subscriber.email::',
            ]);


        $subscriber = Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->subscribeTo($campaign->emailList);


        dispatch(new SendCampaignJob($campaign));

        Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($subscriber) {
            $this->assertEquals("This is a mail sent to {$subscriber->email}", $mail->subject);

            return true;
        });
    }

    /** @test * */
    public function custom_mailable_sends()
    {
        $campaign = (new CampaignFactory())
            ->mailable(TestMailcoachMail::class)
            ->withSubscriberCount(1)
            ->create();

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        $campaign->send();

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "This is the subject from the custom mailable.";
        })->count() > 0);
    }

    /** @test * */
    public function custom_mailable_subject_overrides_campaign_subject()
    {
        $campaign = (new CampaignFactory())
            ->mailable(TestMailcoachMail::class)
            ->withSubscriberCount(1)
            ->create([
                'subject' => 'This subject comes from the campaign',
            ]);

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        $campaign->send();

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "This is the subject from the custom mailable.";
        })->count() > 0);
    }

    /** @test * */
    public function custom_replacers_work_with_campaign_subject()
    {
        $campaign = (new CampaignFactory())
            ->mailable(TestMailcoachMailWithNoSubject::class)
            ->create([
                'subject' => '::customreplacer::',
                'email_html' => '::customreplacer::',
            ]);

        Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->subscribeTo($campaign->emailList);

        config()->set('mailcoach.campaigns.replacers', array_merge(config('mailcoach.campaigns.replacers'), [CustomCampaignReplacer::class]));

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        $campaign->send();

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "The custom replacer works";
        })->count() > 0);
    }

    /** @test * */
    public function custom_replacers_work_with_subject_from_custom_mailable()
    {
        $campaign = (new CampaignFactory())
            ->mailable(TestMailcoachMailWithSubjectReplacer::class)
            ->create();

        Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->subscribeTo($campaign->emailList);

        config()->set('mailcoach.campaigns.replacers', array_merge(config('mailcoach.campaigns.replacers'), [CustomCampaignReplacer::class]));

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        $campaign->send();

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return $message->getSubject() === "Custom Subject: The custom replacer works";
        })->count() > 0);
    }

    /** @test * */
    public function custom_replacers_work_in_body_from_custom_mailable()
    {
        $campaign = (new CampaignFactory())
            ->mailable(TestMailcoachMailWithBodyReplacer::class)
            ->create();

        Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->subscribeTo($campaign->emailList);

        config()->set('mailcoach.campaigns.replacers', array_merge(config('mailcoach.campaigns.replacers'), [CustomCampaignReplacer::class]));

        $campaign->emailList->update(['campaign_mailer' => 'array']);

        dispatch(new SendCampaignJob($campaign));

        $messages = app(MailManager::class)->mailer('array')->getSwiftMailer()->getTransport()->messages();

        $this->assertTrue($messages->filter(function (\Swift_Message $message) {
            return Str::contains($message->getBody(), 'The custom replacer works');
        })->count() > 0);
    }
}