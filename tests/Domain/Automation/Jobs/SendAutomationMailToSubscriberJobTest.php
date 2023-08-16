<?php

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\AutomationMailFactory;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\SendAutomationMailsCommand;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailToSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestClasses\CustomAutomationMailReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithBodyReplacer;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithNoSubject;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithSubjectReplacer;
use Symfony\Component\Mailer\SentMessage;

use function Spatie\Snapshots\assertMatchesHtmlSnapshot;

beforeEach(function () {
    test()->automationMail = (new AutomationMailFactory())
        ->create();

    $this->emailList = EmailList::factory()->create([
        'automation_mailer' => null,
    ]);

    $subscriber = $this->emailList->subscribe('john@doe.com');

    test()->actionSubscriber = ActionSubscriber::create([
        'action_id' => Action::factory()->create()->id,
        'subscriber_id' => $subscriber->id,
    ]);
});

it('can send a automation mail with the mailer from the db', function () {
    $this->emailList->update([
        'automation_mailer' => 'db-mailer',
    ]);

    Event::fake();
    Mail::fake();

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    Artisan::call(SendAutomationMailsCommand::class);

    Mail::assertSent(MailcoachMail::class, 1);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'db-mailer');

    Event::assertDispatched(AutomationMailSentEvent::class, function (AutomationMailSentEvent $event) {
        expect($event->send->automationMail->id)->toEqual(test()->automationMail->id);

        return true;
    });
});

it('can send a automation mail with the mailer from the config', function () {
    Event::fake();
    Mail::fake();

    config()->set('mailcoach.automation.mailer', 'config-mailer');

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    Artisan::call(SendAutomationMailsCommand::class);

    Mail::assertSent(MailcoachMail::class, 1);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->mailer === 'config-mailer');

    Event::assertDispatched(AutomationMailSentEvent::class, function (AutomationMailSentEvent $event) {
        expect($event->send->automationMail->id)->toEqual(test()->automationMail->id);

        return true;
    });
});

it('will not create mailcoach sends if they already have been created if repeat is disabled', function () {
    Event::fake();
    Mail::fake();

    SendFactory::new()->create([
        'subscriber_id' => test()->actionSubscriber->subscriber->id,
        'automation_mail_id' => test()->automationMail->id,
    ]);

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    expect(Send::all())->toHaveCount(1);
});

it('will create mailcoach sends if they already have been created if repeat is enabled', function () {
    Event::fake();
    Mail::fake();

    SendFactory::new()->create([
        'subscriber_id' => test()->actionSubscriber->subscriber->id,
        'automation_mail_id' => test()->automationMail->id,
    ]);

    test()->actionSubscriber->action->automation->update(['repeat_enabled' => true]);

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    expect(Send::all())->toHaveCount(2);
});

it('will use the right subject', function () {
    Event::fake();
    Mail::fake();

    test()->automationMail->subject('my subject');

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    Artisan::call(SendAutomationMailsCommand::class);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $automationMailMail) {
        expect($automationMailMail->subject)->toEqual('my subject');

        return true;
    });
});

it('will use the reply to fields', function () {
    Event::fake();
    Mail::fake();

    test()->automationMail->replyTo('replyto@example.com', 'Reply to John Doe');

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    Artisan::call(SendAutomationMailsCommand::class);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $automationMailMail) {
        return $automationMailMail->build()->hasReplyTo('replyto@example.com', 'Reply to John Doe');
    });
});

it('will prepare the webview', function () {
    Event::fake();
    Mail::fake();

    test()->automationMail->update([
        'html' => 'my html',
        'webview_html' => null,
    ]);

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, test()->actionSubscriber));

    assertMatchesHtmlSnapshot(test()->automationMail->refresh()->webview_html);
});

test('the queue of the send automation mail job can be configured', function () {
    Event::fake();
    Mail::fake();

    Queue::fake();

    config()->set('mailcoach.automation.perform_on_queue.send_automation_mail_to_subscriber_job', 'custom-queue');

    $automationMail = AutomationMail::factory()->create();
    dispatch(new SendAutomationMailToSubscriberJob($automationMail, test()->actionSubscriber));

    Queue::assertPushedOn('custom-queue', SendAutomationMailToSubscriberJob::class);
});

test('personalized placeholders in the subject will be replaced', function () {
    Mail::fake();

    test()->automationMail->update([
        'subject' => 'This is a mail sent to ::subscriber.email::',
    ]);

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, $this->actionSubscriber));

    Artisan::call(SendAutomationMailsCommand::class);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->subject)->toEqual("This is a mail sent to {$this->actionSubscriber->subscriber->email}");

        return true;
    });
});

test('personalized placeholders in the subject will be replaced when using twig', function () {
    Mail::fake();

    test()->automationMail->update([
        'subject' => 'This is a mail sent to {{ subscriber.email }}',
    ]);

    dispatch(new SendAutomationMailToSubscriberJob(test()->automationMail, $this->actionSubscriber));

    Artisan::call(SendAutomationMailsCommand::class);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->subject)->toEqual("This is a mail sent to {$this->actionSubscriber->subscriber->email}");

        return true;
    });
});

test('custom mailable sends', function () {
    test()->automationMail->useMailable(TestMailcoachMail::class);

    config()->set('mailcoach.automation.mailer', 'array');

    test()->automationMail->send(test()->actionSubscriber);

    Artisan::call(SendAutomationMailsCommand::class);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === 'This is the subject from the custom mailable.';
    })->count() > 0);
});

test('custom mailable subject overrides automation mail subject', function () {
    test()->automationMail->useMailable(TestMailcoachMail::class);
    test()->automationMail->update([
        'subject' => 'This subject comes from the automationMail',
    ]);
    config()->set('mailcoach.automation.mailer', 'array');

    test()->automationMail->send(test()->actionSubscriber);

    Artisan::call(SendAutomationMailsCommand::class);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    $this->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === 'This is the subject from the custom mailable.';
    })->count() > 0);
});

test('custom replacers work with automation mail subject', function () {
    test()->automationMail->update([
        'subject' => '::customreplacer::',
        'email_html' => '::customreplacer::',
    ]);
    test()->automationMail->useMailable(TestMailcoachMailWithNoSubject::class);

    config()->set('mailcoach.automation.replacers', array_merge(config('mailcoach.automation.replacers'), [CustomAutomationMailReplacer::class]));
    config()->set('mailcoach.automation.mailer', 'array');

    test()->automationMail->send(test()->actionSubscriber);

    Artisan::call(SendAutomationMailsCommand::class);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    $this->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === 'The custom replacer works';
    })->count() > 0);
});

test('custom replacers work with subject from custom mailable', function () {
    test()->automationMail->useMailable(TestMailcoachMailWithSubjectReplacer::class);

    config()->set('mailcoach.automation.replacers', array_merge(config('mailcoach.automation.replacers'), [CustomAutomationMailReplacer::class]));
    config()->set('mailcoach.automation.mailer', 'array');

    test()->automationMail->send(test()->actionSubscriber);

    Artisan::call(SendAutomationMailsCommand::class);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (SentMessage $message) {
        return $message->getOriginalMessage()->getSubject() === 'Custom Subject: The custom replacer works';
    })->count() > 0);
});

test('custom replacers work in body from custom mailable', function () {
    test()->automationMail->useMailable(TestMailcoachMailWithBodyReplacer::class);

    config()->set('mailcoach.automation.replacers', array_merge(config('mailcoach.automation.replacers'), [CustomAutomationMailReplacer::class]));
    config()->set('mailcoach.automation.mailer', 'array');

    test()->automationMail->send(test()->actionSubscriber);

    Artisan::call(SendAutomationMailsCommand::class);

    $messages = app(MailManager::class)->mailer('array')->getSymfonyTransport()->messages();

    test()->assertTrue($messages->filter(function (SentMessage $message) {
        return Str::contains($message->getOriginalMessage()->toString(), 'The custom replacer works');
    })->count() > 0);
});
