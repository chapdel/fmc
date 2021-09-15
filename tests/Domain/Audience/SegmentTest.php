<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomQueryOnlyShouldSendToJohn;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentAllSubscribers;
use Spatie\Mailcoach\Tests\TestClasses\TestSegmentQueryOnlyJohn;



beforeEach(function () {
    Mail::fake();

    test()->campaign = Campaign::factory()->create();

    test()->emailList = EmailList::factory()->create();
});

it('will not send a mail if it is not subscribed to the list of the campaign even if the segment selects it', function () {
    Subscriber::factory()->create();

    test()->campaign->segment(TestSegmentAllSubscribers::class)->sendTo(test()->emailList);

    Mail::assertNothingSent();
});

it('can segment a test by using a query', function () {
    test()->emailList->subscribe('john@example.com');
    test()->emailList->subscribe('jane@example.com');

    test()->campaign
        ->segment(TestSegmentQueryOnlyJohn::class)
        ->sendTo(test()->emailList);

    Mail::assertSent(MailcoachMail::class, 1);

    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('john@example.com'));

    Mail::assertNotSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('jane@example.com'));
});

it('can segment a test by using should send', function () {
    test()->emailList->subscribe('john@example.com');
    test()->emailList->subscribe('jane@example.com');
    test()->campaign
        ->segment(TestCustomQueryOnlyShouldSendToJohn::class)
        ->sendTo(test()->emailList);
    Mail::assertSent(MailcoachMail::class, 1);
    Mail::assertSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('john@example.com'));
    Mail::assertNotSent(MailcoachMail::class, fn (MailcoachMail $mail) => $mail->hasTo('jane@example.com'));
    expect(test()->campaign->fresh()->isSent())->toBeTrue();
});
