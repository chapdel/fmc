<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailSentEvent;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->action = resolve(SendMailAction::class);

    /** @var Send $send */
    test()->send = Send::factory()->create(['campaign_id' => null]);

    Mail::fake();
    Event::fake();
});

it('sends a pending send', function () {
    test()->action->execute(test()->send);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        test()->assertTrue($mail->hasTo(test()->send->subscriber->email));

        return true;
    });

    Event::assertDispatched(AutomationMailSentEvent::class);

    test()->assertTrue(test()->send->wasAlreadySent());
});

it('sets reply to', function () {
    test()->send->automationMail->update([
        'reply_to_email' => 'foo@bar.com',
        'reply_to_name' => 'Foo',
    ]);

    test()->action->execute(test()->send);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        test()->assertTrue($mail->hasReplyTo('foo@bar.com', 'Foo'));

        return true;
    });
});

it('wont send again if the send was already sent', function () {
    test()->action->execute(test()->send);
    test()->action->execute(test()->send);

    Mail::assertSent(MailcoachMail::class, 1);
    Event::assertDispatched(AutomationMailSentEvent::class, 1);
});
