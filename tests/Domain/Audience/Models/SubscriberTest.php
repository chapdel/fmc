<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();

    Mail::fake();
});

it('will only subscribe a subscriber once', function () {
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeFalse();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    expect(Subscriber::count())->toEqual(1);
});

it('can resubscribe someone', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    $subscriber->unsubscribe();
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeFalse();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
});

it('will send a confirmation mail if the list requires double optin', function () {
    test()->emailList->update([
        'requires_confirmation' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeFalse();

    Mail::assertQueued(ConfirmSubscriberMail::class, function (ConfirmSubscriberMail $mail) use ($subscriber) {
        expect($mail->subscriber->uuid)->toEqual($subscriber->uuid);

        return true;
    });
});

it('will send a welcome mail if the list has welcome mails', function () {
    test()->emailList->update([
        'send_welcome_mail' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) use ($subscriber) {
        expect($mail->subscriber->uuid)->toEqual($subscriber->uuid);

        return true;
    });
});

it('will only send a welcome mail once', function () {
    test()->emailList->update([
        'send_welcome_mail' => true,
    ]);

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    Mail::assertQueued(WelcomeMail::class, 1);
});

it('can immediately subscribe someone and not send a mail even with double opt in enabled', function () {
    test()->emailList->update([
        'requires_confirmation' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    Mail::assertNotQueued(ConfirmSubscriberMail::class);
});

test('no email will be sent when adding someone that was already subscribed', function () {
    $subscriber = Subscriber::factory()->create();
    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);
    $subscriber->emailList->update(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    Mail::assertNothingQueued();
});

it('can get all sends', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $send->subscriber;

    $sends = $subscriber->sends;

    expect($sends)->toHaveCount(1);

    expect($sends->first()->uuid)->toEqual($send->uuid);
});

it('can get all opens', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_opens' => true]);

    $send->registerOpen();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = $send->subscriber;

    $opens = $subscriber->opens;
    expect($opens)->toHaveCount(1);

    expect($subscriber->opens->first()->send->uuid)->toEqual($send->uuid);
    expect($subscriber->opens->first()->subscriber->uuid)->toEqual($subscriber->uuid);
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
    expect($clicks)->toHaveCount(3);

    $uniqueClicks = $subscriber->uniqueClicks;
    expect($uniqueClicks)->toHaveCount(2);

    test()->assertEquals(
        ['https://example.com','https://another-domain.com'],
        $uniqueClicks->pluck('link.url')->toArray()
    );
});

it('can scope on campaign sends', function () {
    $subscriber1 = Subscriber::factory()->create();
    Subscriber::factory()->create();
    $campaign = Campaign::factory()->create();

    expect(Subscriber::withoutSendsForCampaign($campaign)->count())->toBe(2);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'subscriber_id' => $subscriber1,
    ]);

    expect(Subscriber::withoutSendsForCampaign($campaign)->count())->toBe(1);
});
