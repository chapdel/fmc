<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\App\Queries\Filters\SearchFilter;

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

it('can immediately subscribe someone and not send a mail even with double opt in enabled', function () {
    test()->emailList->update([
        'requires_confirmation' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    expect($subscriber->status)->toEqual(SubscriptionStatus::Subscribed);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    Mail::assertNotQueued(ConfirmSubscriberMail::class);
});

test('no email will be sent when adding someone that was already subscribed', function () {
    $subscriber = Subscriber::factory()->create();
    expect($subscriber->status)->toEqual(SubscriptionStatus::Subscribed);
    $subscriber->emailList->update(['requires_confirmation' => true]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    expect($subscriber->status)->toEqual(SubscriptionStatus::Subscribed);

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
    $send->campaign->update();

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
        ['https://example.com', 'https://another-domain.com'],
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

it('can sync tags', function () {
    $subscriber = Subscriber::factory()->create();

    $subscriber->syncTags(['one', 'two']);

    expect(Tag::count())->toBe(2);
});

it('can sync tags with null', function () {
    $subscriber = Subscriber::factory()->create();

    $subscriber->syncTags(null);

    expect(Tag::count())->toBe(0);
});

it('can sync preference tags', function () {
    $subscriber = Subscriber::factory()->create();

    $subscriber->syncTags(['one', 'two']);

    Tag::where('name', 'two')->update(['visible_in_preferences' => true]);

    $subscriber->syncPreferenceTags([]);

    expect($subscriber->fresh()->tags->count())->toBe(1);
});

it('can sync preference tags with null', function () {
    $subscriber = Subscriber::factory()->create();

    $subscriber->syncTags(['one', 'two']);

    Tag::where('name', 'two')->update(['visible_in_preferences' => true]);

    $subscriber->syncPreferenceTags(null);

    expect($subscriber->fresh()->tags->count())->toBe(1);
});

it('can retrieve subscribers by extra attributes', function () {
    Subscriber::factory()->create();
    $subscriber = Subscriber::factory()->create();

    $subscriber->extra_attributes->external_id = 12345;
    $subscriber->save();

    expect(Subscriber::query()->withExtraAttributes(['external_id' => 12345])->count())->toBe(1);
});

it('can search on email', function () {
    Subscriber::factory()->create(['email' => 'john@doe.com']);
    Subscriber::factory()->create(['email' => 'jane@doe.com']);

    expect(Subscriber::search('john@doe.com')->count())->toBe(1);
});

it('can search on first name', function () {
    Subscriber::all()->each->delete();
    Subscriber::factory()->create(['first_name' => 'John Doe']);
    Subscriber::factory()->create(['first_name' => 'Jane Doe']);

    expect(Subscriber::search('John')->count())->toBe(1);
    expect(Subscriber::search('Doe', 10)->count())->toBe(2);
});

it('can search on last name', function () {
    Subscriber::factory()->create(['last_name' => 'John Doe', 'email' => 'irrelevant']);
    Subscriber::factory()->create(['last_name' => 'Jane Doe', 'email' => 'irrelevant']);

    expect(Subscriber::search('John')->count())->toBe(1);
    expect(Subscriber::search('Doe', 10)->count())->toBe(2);
});

it('can search on encrypted email', function () {
    config()->set('mailcoach.encryption.enabled', true);
    config()->set('ciphersweet.providers.string.key', 'd3cc14e44763208f95af769f16d97cabdc815ec6416700b0bee23545d8375188');

    Subscriber::factory()->create(['email' => 'john@doe.com']);
    Subscriber::factory()->create(['email' => 'jane@doe.com']);

    $filter = new SearchFilter();

    expect($filter(Subscriber::query(), 'john@doe.com', 'search')->count())->toBe(1);
});

it('can search on encrypted first name', function () {
    config()->set('mailcoach.encryption.enabled', true);
    config()->set('ciphersweet.providers.string.key', 'd3cc14e44763208f95af769f16d97cabdc815ec6416700b0bee23545d8375188');

    Subscriber::factory()->create(['first_name' => 'John']);
    Subscriber::factory()->create(['first_name' => 'Jane']);

    $filter = new SearchFilter();

    expect($filter(Subscriber::query(), 'John', 'search')->count())->toBe(1);
});

it('can search on encrypted last name', function () {
    config()->set('mailcoach.encryption.enabled', true);
    config()->set('ciphersweet.providers.string.key', 'd3cc14e44763208f95af769f16d97cabdc815ec6416700b0bee23545d8375188');

    Subscriber::factory()->create(['last_name' => 'John Doe']);
    Subscriber::factory()->create(['last_name' => 'Jane Doe']);

    $filter = new SearchFilter();

    expect($filter(Subscriber::query(), 'John Doe', 'search')->count())->toBe(1);
});

it('can be converted to an export row', function () {
    $subscriber = Subscriber::factory()->create([
        'email' => 'john@doe.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'subscribed_at' => now(),
        'unsubscribed_at' => now(),
        'extra_attributes' => [
            'foo' => 'bar',
            'baz' => 'bad',
        ],
    ]);

    $subscriber->syncTags(['one', 'two']);

    expect($subscriber->toExportRow())->toBe([
        'email' => 'john@doe.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'tags' => 'one;two',
        'subscribed_at' => now()->format('Y-m-d H:i:s'),
        'unsubscribed_at' => now()->format('Y-m-d H:i:s'),
        'baz' => 'bad',
        'foo' => 'bar',
    ]);

    $subscriber->update(['extra_attributes' => null]);

    expect($subscriber->toExportRow())->toBe([
        'email' => 'john@doe.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'tags' => 'one;two',
        'subscribed_at' => now()->format('Y-m-d H:i:s'),
        'unsubscribed_at' => now()->format('Y-m-d H:i:s'),
    ]);
});
