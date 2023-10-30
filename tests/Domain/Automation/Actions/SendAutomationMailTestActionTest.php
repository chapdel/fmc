<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;

beforeEach(function () {
    test()->action = resolve(SendAutomationMailTestAction::class);

    /** @var AutomationMail $automationMail */
    test()->automationMail = AutomationMail::factory()->create();
    test()->automationMail->contentItem->update(['subject' => 'A subject']);

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
