<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Exceptions\CouldNotSubscribe;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailList;
use Spatie\Mailcoach\Tests\TestClasses\CustomSubscriber;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create(['name' => 'Mailcoach Subscribers']);
});

it('can add a subscriber to a list', function () {
    $subscriber = test()->emailList->subscribe('john@example.com');

    test()->assertEquals('john@example.com', $subscriber->email);
});

it('can add a subscriber with extra attributes to a list', function () {
    $attributes = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'extra_attributes' => ['key 1' => 'Value 1', 'key 2' => 'Value 2'],
    ];

    $subscriber = test()->emailList->subscribe('john@example.com', $attributes)->refresh();

    test()->assertEquals('john@example.com', $subscriber->email);
    test()->assertEquals('John', $subscriber->first_name);
    test()->assertEquals('Doe', $subscriber->last_name);
    test()->assertEquals($attributes['extra_attributes'], $subscriber->extra_attributes->all());
});

test('when adding someone that was already subscribed no new subscription will be created', function () {
    test()->emailList->subscribe('john@example.com');
    test()->emailList->subscribe('john@example.com');

    test()->assertEquals(1, Subscriber::count());
});

it('can unsubscribe someone', function () {
    test()->emailList->subscribe('john@example.com');

    test()->assertTrue(test()->emailList->unsubscribe('john@example.com'));
    test()->assertFalse(test()->emailList->unsubscribe('non-existing-subscriber@example.com'));

    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, Subscriber::first()->status);
});

it('can get all subscribers that are subscribed', function () {
    test()->emailList->subscribe('john@example.com');
    test()->emailList->subscribe('jane@example.com');
    test()->emailList->unsubscribe('john@example.com');

    $subscribers = test()->emailList->subscribers;
    test()->assertCount(1, $subscribers);
    test()->assertEquals('jane@example.com', $subscribers->first()->email);

    $subscribers = test()->emailList->allSubscribers;
    test()->assertCount(2, $subscribers);
});

it('can subscribe someone immediately even if double opt in is enabled', function () {
    Mail::fake();

    test()->emailList->update(['requires_confirmation' => true]);

    test()->emailList->subscribeSkippingConfirmation('john@example.com');

    Mail::assertNothingQueued();

    test()->assertEquals('john@example.com', test()->emailList->subscribers->first()->email);
});

it('cannot subscribe an invalid email', function () {
    test()->expectException(CouldNotSubscribe::class);

    test()->emailList->subscribe('invalid-email');
});

it('can get the status of a subscription', function () {
    test()->assertNull(test()->emailList->getSubscriptionStatus('john@example.com'));

    test()->emailList->subscribe('john@example.com');

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, test()->emailList->getSubscriptionStatus('john@example.com'));
});

it('can summarize an email list', function () {
    TestTime::freeze();

    test()->assertEquals([
        'total_number_of_subscribers' => 0,
        'total_number_of_subscribers_gained' => 0,
        'total_number_of_unsubscribes_gained' => 0,
    ], test()->emailList->summarize(now()->subWeek()));

    $subscriber = Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    test()->assertEquals([
        'total_number_of_subscribers' => 1,
        'total_number_of_subscribers_gained' => 1,
        'total_number_of_unsubscribes_gained' => 0,
    ], test()->emailList->summarize(now()->subWeek()));

    $subscriber->unsubscribe();

    test()->assertEquals([
        'total_number_of_subscribers' => 0,
        'total_number_of_subscribers_gained' => 1,
        'total_number_of_unsubscribes_gained' => 1,
    ], test()->emailList->summarize(now()->subWeek()));

    Subscriber::createWithEmail('jane@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    test()->assertEquals([
        'total_number_of_subscribers' => 1,
        'total_number_of_subscribers_gained' => 2,
        'total_number_of_unsubscribes_gained' => 1,
    ], test()->emailList->summarize(now()->subWeek()));

    TestTime::addWeek();

    test()->assertEquals([
        'total_number_of_subscribers' => 1,
        'total_number_of_subscribers_gained' => 0,
        'total_number_of_unsubscribes_gained' => 0,
    ], test()->emailList->summarize(now()->subWeek()));

    Subscriber::createWithEmail('paul@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    test()->assertEquals([
        'total_number_of_subscribers' => 2,
        'total_number_of_subscribers_gained' => 1,
        'total_number_of_unsubscribes_gained' => 0,
    ], test()->emailList->summarize(now()->subWeek()));
});

it('can reference tags and segments when using a custom model', function () {
    Tag::factory(2)->create(['email_list_id' => test()->emailList->id]);
    TagSegment::create(['name' => 'testSegment', 'email_list_id' => test()->emailList->id]);

    Config::set("mailcoach.models.email_list", CustomEmailList::class);
    Config::set("mailcoach.models.subscriber", CustomSubscriber::class);

    $list = CustomEmailList::find(test()->emailList->id);

    test()->assertEquals(2, $list->tags()->count());
    test()->assertEquals(1, $list->segments()->count());
});
