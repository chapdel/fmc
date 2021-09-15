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

    expect($subscriber->email)->toEqual('john@example.com');
});

it('can add a subscriber with extra attributes to a list', function () {
    $attributes = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'extra_attributes' => ['key 1' => 'Value 1', 'key 2' => 'Value 2'],
    ];

    $subscriber = test()->emailList->subscribe('john@example.com', $attributes)->refresh();

    expect($subscriber->email)->toEqual('john@example.com');
    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');
    expect($subscriber->extra_attributes->all())->toEqual($attributes['extra_attributes']);
});

test('when adding someone that was already subscribed no new subscription will be created', function () {
    test()->emailList->subscribe('john@example.com');
    test()->emailList->subscribe('john@example.com');

    expect(Subscriber::count())->toEqual(1);
});

it('can unsubscribe someone', function () {
    test()->emailList->subscribe('john@example.com');

    expect(test()->emailList->unsubscribe('john@example.com'))->toBeTrue();
    expect(test()->emailList->unsubscribe('non-existing-subscriber@example.com'))->toBeFalse();

    expect(Subscriber::first()->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);
});

it('can get all subscribers that are subscribed', function () {
    test()->emailList->subscribe('john@example.com');
    test()->emailList->subscribe('jane@example.com');
    test()->emailList->unsubscribe('john@example.com');

    $subscribers = test()->emailList->subscribers;
    expect($subscribers)->toHaveCount(1);
    expect($subscribers->first()->email)->toEqual('jane@example.com');

    $subscribers = test()->emailList->allSubscribers;
    expect($subscribers)->toHaveCount(2);
});

it('can subscribe someone immediately even if double opt in is enabled', function () {
    Mail::fake();

    test()->emailList->update(['requires_confirmation' => true]);

    test()->emailList->subscribeSkippingConfirmation('john@example.com');

    Mail::assertNothingQueued();

    expect(test()->emailList->subscribers->first()->email)->toEqual('john@example.com');
});

it('cannot subscribe an invalid email', function () {
    test()->expectException(CouldNotSubscribe::class);

    test()->emailList->subscribe('invalid-email');
});

it('can get the status of a subscription', function () {
    expect(test()->emailList->getSubscriptionStatus('john@example.com'))->toBeNull();

    test()->emailList->subscribe('john@example.com');

    expect(test()->emailList->getSubscriptionStatus('john@example.com'))->toEqual(SubscriptionStatus::SUBSCRIBED);
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

    expect($list->tags()->count())->toEqual(2);
    expect($list->segments()->count())->toEqual(1);
});
