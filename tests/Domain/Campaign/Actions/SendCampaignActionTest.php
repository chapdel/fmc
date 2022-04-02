<?php

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\CreateCampaignSendJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestClasses\CustomCampaignReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithBodyReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithNoSubject;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithSubjectReplacer;
use Spatie\TestTime\TestTime;
use Symfony\Component\Mailer\SentMessage;

beforeEach(function () {
    test()->campaign = (new CampaignFactory())
        ->withSubscriberCount(3)
        ->create();

    test()->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    test()->action = resolve(SendCampaignAction::class);
});

it('can send a campaign with the correct mailer', function () {
    Event::fake();
    Mail::fake();

    test()->campaign->send();
    test()->action->execute(test()->campaign);

    Mail::assertSent(MailcoachMail::class, 3);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'some-mailer');

    Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) {
        expect($event->campaign->id)->toEqual(test()->campaign->id);

        return true;
    });

    test()->campaign->refresh();
    expect(test()->campaign->status)->toEqual(CampaignStatus::SENT);
    expect(test()->campaign->sent_to_number_of_subscribers)->toEqual(3);
});

it('will throttle sending mail', function () {
    config()->set('mailcoach.campaigns.throttling.allowed_number_of_jobs_in_timespan', 2);
    config()->set('mailcoach.campaigns.throttling.timespan_in_seconds', 3);

    Mail::fake();
    TestTime::unfreeze();

    test()->campaign->send();
    test()->action->execute(test()->campaign);
    Mail::assertSent(MailcoachMail::class, 3);

    $jobDispatchTimes = Send::get()
        ->map(function (Send $send) {
            return $send->sending_job_dispatched_at;
        })
        ->toArray();

    [$sendTime1, $sendTime2, $sendTime3] = $jobDispatchTimes;

    expect($sendTime1->diffInSeconds($sendTime2))->toEqual(0);
    expect($sendTime2->diffInSeconds($sendTime3))->toEqual(3);
});

it('will not create mailcoach sends if they already have been created', function () {
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

    $campaign->send();
    test()->action->execute($campaign);

    expect(Send::all())->toHaveCount(1);
});

it('will dispatch create jobs and not dispatch twice', function () {
    Queue::fake();

    $emailList = EmailList::factory()->create();

    $campaign = Campaign::factory()->create([
        'email_list_id' => $emailList->id,
    ]);

    Subscriber::factory()->create([
        'email_list_id' => $emailList->id,
        'subscribed_at' => now(),
    ]);

    $campaign->send();
    test()->action->execute($campaign);

    expect($campaign->fresh()->allSendsCreated())->toBeFalse();

    Queue::assertPushed(CreateCampaignSendJob::class, 1);

    test()->action->execute($campaign);

    expect($campaign->fresh()->allSendsCreated())->toBeFalse();
    Queue::assertPushed(CreateCampaignSendJob::class, 1);
});

it('will set all sends created when they are', function () {
    Queue::fake();

    $emailList = EmailList::factory()->create();

    $campaign = Campaign::factory()->create([
        'email_list_id' => $emailList->id,
    ]);

    $subscriber = Subscriber::factory()->create([
        'email_list_id' => $emailList->id,
        'subscribed_at' => now(),
    ]);

    $campaign->send();
    test()->action->execute($campaign);

    expect($campaign->fresh()->allSendsCreated())->toBeFalse();

    Queue::assertPushed(CreateCampaignSendJob::class, 1);
    Queue::assertPushed(SendCampaignMailJob::class, 0);

    test()->action->execute($campaign);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'subscriber_id' => $subscriber->id,
    ]);

    test()->action->execute($campaign);

    expect($campaign->fresh()->allSendsCreated())->toBeTrue();
    Queue::assertPushed(CreateCampaignSendJob::class, 1);
    Queue::assertPushed(SendCampaignMailJob::class, 1);
});

it('will use the right subject', function () {
    Event::fake();
    Mail::fake();

    test()->campaign->subject('my subject');

    test()->campaign->send();
    test()->action->execute(test()->campaign);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $campaignMail) {
        expect($campaignMail->subject)->toEqual('my subject');

        return true;
    });
});

it('will use the reply to fields', function () {
    Event::fake();
    Mail::fake();

    test()->campaign->replyTo('replyto@example.com', 'Reply to John Doe');

    test()->campaign->send();
    test()->action->execute(test()->campaign);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $campaignMail) {
        return $campaignMail->build()->hasReplyTo('replyto@example.com', 'Reply to John Doe');
    });
});

test('a campaign that was sent will not be sent again', function () {
    Event::fake();
    Mail::fake();

    expect(test()->campaign->wasAlreadySent())->toBeFalse();
    test()->campaign->send();
    test()->action->execute(test()->campaign);
    expect(test()->campaign->refresh()->wasAlreadySent())->toBeTrue();
    Mail::assertSent(MailcoachMail::class, 3);

    test()->action->execute(test()->campaign);
    Mail::assertSent(MailcoachMail::class, 3);
    Event::assertDispatched(CampaignSentEvent::class, 1);
});

it('will prepare the webview', function () {
    Event::fake();
    Mail::fake();

    test()->campaign->update([
        'html' => 'my html',
        'webview_html' => null,
    ]);

    test()->campaign->send();
    test()->action->execute(test()->campaign);

    test()->assertMatchesHtmlSnapshotWithoutWhitespace(test()->campaign->refresh()->webview_html);
});

it('will not send invalid html', function () {
    Event::fake();
    Mail::fake();

    test()->campaign->update([
        'track_clicks' => true,
        'html' => '<qsdfqlsmdkjm><<>><<',
    ]);

    test()->expectException(CouldNotSendCampaign::class);

    test()->campaign->send();
    test()->action->execute(test()->campaign);
});

test('regular placeholders in the subject will be replaced', function () {
    Mail::fake();

    $campaign = (new CampaignFactory())
        ->withSubscriberCount(1)
        ->create([
            'subject' => 'This is a mail sent to ::list.name::',
        ]);

    $campaign->emailList->update(['name' => 'my list']);

    $campaign->send();
    test()->action->execute($campaign);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->subject)->toEqual("This is a mail sent to my list");

        return true;
    });
});

test('personalized placeholders in the subject will be replaced', function () {
    Mail::fake();

    $campaign = (new CampaignFactory())
        ->create([
            'subject' => 'This is a mail sent to ::subscriber.email::',
        ]);


    $subscriber = Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo($campaign->emailList);


    $campaign->send();
    test()->action->execute($campaign);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) use ($subscriber) {
        expect($mail->subject)->toEqual("This is a mail sent to {$subscriber->email}");

        return true;
    });
});

test('custom mailable sends', function () {
    $campaign = (new CampaignFactory())
        ->mailable(TestMailcoachMail::class)
        ->withSubscriberCount(1)
        ->create();

    $campaign->emailList->update(['campaign_mailer' => 'array']);

    $campaign->send();
    test()->action->execute($campaign);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    $this->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === "This is the subject from the custom mailable.";
    })->count() > 0);
});

test('custom mailable subject overrides campaign subject', function () {
    $campaign = (new CampaignFactory())
        ->mailable(TestMailcoachMail::class)
        ->withSubscriberCount(1)
        ->create([
            'subject' => 'This subject comes from the campaign',
        ]);

    $campaign->emailList->update(['campaign_mailer' => 'array']);

    $campaign->send();
    test()->action->execute($campaign);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    $this->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === "This is the subject from the custom mailable.";
    })->count() > 0);
});

test('custom replacers work with campaign subject', function () {
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
    test()->action->execute($campaign);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === "The custom replacer works";
    })->count() > 0);
});

test('custom replacers work with subject from custom mailable', function () {
    $campaign = (new CampaignFactory())
        ->mailable(TestMailcoachMailWithSubjectReplacer::class)
        ->create();

    Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo($campaign->emailList);

    config()->set('mailcoach.campaigns.replacers', array_merge(config('mailcoach.campaigns.replacers'), [CustomCampaignReplacer::class]));

    $campaign->emailList->update(['campaign_mailer' => 'array']);

    $campaign->send();
    test()->action->execute($campaign);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === "Custom Subject: The custom replacer works";
    })->count() > 0);
});

test('custom replacers work in body from custom mailable', function () {
    $campaign = (new CampaignFactory())
        ->mailable(TestMailcoachMailWithBodyReplacer::class)
        ->create();

    Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo($campaign->emailList);

    config()->set('mailcoach.campaigns.replacers', array_merge(config('mailcoach.campaigns.replacers'), [CustomCampaignReplacer::class]));

    $campaign->emailList->update(['campaign_mailer' => 'array']);

    $campaign->send();
    test()->action->execute($campaign);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (SentMessage $message) {
        return Str::contains($message->getOriginalMessage()->toString(), 'The custom replacer works');
    })->count() > 0);
});
