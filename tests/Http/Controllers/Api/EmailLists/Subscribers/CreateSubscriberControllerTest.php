<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    test()->attributes = [
        'email_list_id' => test()->emailList->id,
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];

    Mail::fake();
});

it('can create a subscriber using the api', function () {
    $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), test()->attributes)
        ->assertSuccessful();

    test()->assertDatabaseHas(test()->getSubscriberTableName(), test()->attributes);

    test()->assertEquals(SubscriptionStatus::UNCONFIRMED, Subscriber::first()->status);

    Mail::assertQueued(ConfirmSubscriberMail::class);
});

it('can skip the confirmation while subscribing', function () {
    $attributes = test()->attributes;

    $attributes['skip_confirmation'] = true;

    $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), $attributes)
        ->assertSuccessful();

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, Subscriber::first()->status);

    Mail::assertNotQueued(ConfirmSubscriberMail::class);
});

it('can create a subscriber with extra attributes', function () {
    test()->attributes['extra_attributes'] = [
        'foo' => 'bar',
    ];

    $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), test()->attributes)
        ->assertSuccessful();

    test()->assertSame(test()->attributes['extra_attributes'], Subscriber::first()->extra_attributes->toArray());
});

it('can create a subscriber with tags', function () {
    test()->attributes['tags'] = ['foo', 'bar'];

    $response = $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), test()->attributes);

    $response->assertSuccessful();
    $response->assertJsonFragment(['tags' => ['foo', 'bar']]);
    test()->assertTrue(Subscriber::first()->hasTag('foo'));
    test()->assertTrue(Subscriber::first()->hasTag('bar'));
});
