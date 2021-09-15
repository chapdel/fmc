<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

beforeEach(function () {
    test()->action = resolve(SendAutomationMailTestAction::class);

    /** @var AutomationMail $automationMail */
    test()->automationMail = AutomationMail::factory()->create();

    Mail::fake();
    Event::fake();
});

it('sends a test mail', function () {
    test()->action->execute(test()->automationMail, 'john@doe.com');

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->hasTo('john@doe.com'))->toBeTrue();

        return true;
    });
});

it('sets reply to', function () {
    test()->automationMail->update([
        'reply_to_email' => 'foo@bar.com',
        'reply_to_name' => 'Foo',
    ]);

    test()->action->execute(test()->automationMail, 'john@doe.com');

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->hasReplyTo('foo@bar.com', 'Foo'))->toBeTrue();

        return true;
    });
});
