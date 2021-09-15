<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    $this->attributes = [
        'email_list_id' => test()->emailList->id,
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];

    Mail::fake();
});

it('can create a subscriber using the api', function () {
    $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), $this->attributes)
        ->assertSuccessful();

    test()->assertDatabaseHas(test()->getSubscriberTableName(), $this->attributes);

    expect(Subscriber::first()->status)->toEqual(SubscriptionStatus::UNCONFIRMED);

    Mail::assertQueued(ConfirmSubscriberMail::class);
});

it('can skip the confirmation while subscribing', function () {
    $attributes = $this->attributes;

    $attributes['skip_confirmation'] = true;

    $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), $attributes)
        ->assertSuccessful();

    expect(Subscriber::first()->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    Mail::assertNotQueued(ConfirmSubscriberMail::class);
});

it('can create a subscriber with extra attributes', function () {
    $this->attributes['extra_attributes'] = [
        'foo' => 'bar',
    ];

    $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), $this->attributes)
        ->assertSuccessful();

    expect(Subscriber::first()->extra_attributes->toArray())->toBe($this->attributes['extra_attributes']);
});

it('can create a subscriber with tags', function () {
    $this->attributes['tags'] = ['foo', 'bar'];

    $response = $this
        ->postJson(action([SubscribersController::class, 'store'], test()->emailList), $this->attributes);

    $response->assertSuccessful();
    $response->assertJsonFragment(['tags' => ['foo', 'bar']]);
    expect(Subscriber::first()->hasTag('foo'))->toBeTrue();
    expect(Subscriber::first()->hasTag('bar'))->toBeTrue();
});
