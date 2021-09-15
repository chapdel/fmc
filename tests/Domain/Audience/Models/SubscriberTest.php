<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();

    Mail::fake();
});

it('will only subscribe a subscriber once', function () {
    test()->assertFalse(test()->emailList->isSubscribed('john@example.com'));

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    test()->assertEquals(1, Subscriber::count());
});

it('can resubscribe someone', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    $subscriber->unsubscribe();
    test()->assertFalse(test()->emailList->isSubscribed('john@example.com'));

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));
});

it('will send a confirmation mail if the list requires double optin', function () {
    test()->emailList->update([
        'requires_confirmation' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertFalse(test()->emailList->isSubscribed('john@example.com'));

    Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) use ($subscriber) {
        test()->assertEquals($subscriber->uuid, $mail->subscriber->uuid);

        return true;
    });
});

it('will send a welcome mail if the list has welcome mails', function () {
    test()->emailList->update([
        'send_welcome_mail' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) use ($subscriber) {
        test()->assertEquals($subscriber->uuid, $mail->subscriber->uuid);

        return true;
    });
});

it('will only send a welcome mail once', function () {
    test()->emailList->update([
        'send_welcome_mail' => true,
    ]);

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    Mail::assertQueued(WelcomeMail::class, 1);
});

it('can immediately subscribe someone and not send a mail even with double opt in enabled', function () {
    test()->emailList->update([
        'requires_confirmation' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    Mail::assertNotQueued(ConfirmSubscriberMail::class);
});

test('no email will be sent when adding someone that was already subscribed', function () {
    $subscriber = Subscriber::factory()->create();
    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);
    $subscriber->emailList->update(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

    Mail::assertNothingQueued();
});

it('can get all sends', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $send->subscriber;

    $sends = $subscriber->sends;

    test()->assertCount(1, $sends);

    test()->assertEquals($send->uuid, $sends->first()->uuid);
});

it('can get all opens', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_opens' => true]);

    $send->registerOpen();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $send->subscriber;

    $opens = $subscriber->opens;
    test()->assertCount(1, $opens);

    test()->assertEquals($send->uuid, $subscriber->opens->first()->send->uuid);
    test()->assertEquals($subscriber->uuid, $subscriber->opens->first()->subscriber->uuid);
});

it('can get all clicks', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $send->registerClick('https://example.com');
    $send->registerClick('https://another-domain.com');
    $send->registerClick('https://example.com');

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $send->subscriber;

    $clicks = $subscriber->clicks;
    test()->assertCount(3, $clicks);

    $uniqueClicks = $subscriber->uniqueClicks;
    test()->assertCount(2, $uniqueClicks);

    test()->assertEquals(
        ['https://example.com','https://another-domain.com'],
        $uniqueClicks->pluck('link.url')->toArray()
    );
});
