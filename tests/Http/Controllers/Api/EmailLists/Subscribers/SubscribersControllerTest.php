<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->emailList = EmailList::factory()->create();
});

it('can list all subscribers of an email list', function () {
    $subscribers = Subscriber::factory(3)->create([
        'email_list_id' => test()->emailList->id,
    ]);

    $response = $this
        ->getJson(action([SubscribersController::class, 'index'], test()->emailList))
        ->assertSuccessful()
        ->assertJsonCount(3, 'data');

    foreach ($subscribers as $subscriber) {
        $response->assertJsonFragment(['email' => $subscriber->email]);
    }
});

it('can filter on email', function () {
    $subscribers = Subscriber::factory(3)->create([
        'email_list_id' => test()->emailList->id,
    ]);

    $response = $this
        ->getJson(action([SubscribersController::class, 'index'], test()->emailList).'?filter[email]='.$subscribers[0]->email)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');

    $response->assertJsonFragment(['email' => $subscribers[0]->email]);
});

it('can fuzzy filter on email', function () {
    $subscribers = Subscriber::factory(3)->create([
        'email_list_id' => test()->emailList->id,
    ]);

    $response = $this
        ->getJson(action([SubscribersController::class, 'index'], test()->emailList).'?filter[search]='.$subscribers[0]->email)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');

    $response->assertJsonFragment(['email' => $subscribers[0]->email]);
});

it('can filter on subscription status', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create([
        'email_list_id' => test()->emailList->id,
    ]);

    $endpoint = action([SubscribersController::class, 'index'], test()->emailList).'?filter[status]=unsubscribed';

    $this
        ->getJson($endpoint)
        ->assertSuccessful()
        ->assertJsonCount(0, 'data');

    $subscriber->unsubscribe();

    $this
        ->getJson($endpoint)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can show a subscriber', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    $this
        ->getJson(action([SubscribersController::class, 'show'], $subscriber->uuid))
        ->assertSuccessful()
        ->assertJsonFragment(['email' => $subscriber->email]);
});

it('can show a subscriber by uuid', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    $this
        ->getJson(action([SubscribersController::class, 'show'], $subscriber->uuid))
        ->assertSuccessful()
        ->assertJsonFragment(['email' => $subscriber->email]);
});

it('wont confuse id and uuid', function () {
    /** @var Subscriber $subscriber1 */
    $subscriber1 = Subscriber::factory()->create([
        'uuid' => '150ecdda-14b8-49aa-b1a1-6a07d42c2a76',
    ]);

    Subscriber::factory()->create([
        'uuid' => 'some-other-uuid',
        'id' => 150,
    ]);

    $this
        ->getJson(action([SubscribersController::class, 'show'], '150ecdda-14b8-49aa-b1a1-6a07d42c2a76'))
        ->assertSuccessful()
        ->assertJsonFragment(['email' => $subscriber1->email]);
});

it('can delete a subscriber', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    $this
        ->deleteJson(action([SubscribersController::class, 'destroy'], $subscriber->uuid))
        ->assertSuccessful();

    expect(Subscriber::all())->toHaveCount(0);
});

it('can update a subscriber', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()
        ->for(EmailList::factory(), 'emailList')
        ->create();

    $attributes = [
        'email' => 'janedoe@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'tags' => ['test1', 'test2'],
    ];
    $this
        ->patchJson(action([SubscribersController::class, 'update'], $subscriber->uuid), $attributes)
        ->assertSuccessful();

    $subscriber->refresh();
    expect($subscriber->email)->toEqual($attributes['email']);
    expect($subscriber->first_name)->toEqual($attributes['first_name']);
    expect($subscriber->last_name)->toEqual($attributes['last_name']);
    expect($subscriber->tags->pluck('name')->toArray())->toEqual($attributes['tags']);
});

it('can update a subscriber with extra attributes', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()
        ->for(EmailList::factory(), 'emailList')
        ->create();

    $attributes = [
        'extra_attributes' => [
            'foo' => 'bar',
        ],
    ];

    $this
        ->patchJson(action([SubscribersController::class, 'update'], $subscriber->uuid), $attributes)
        ->assertSuccessful();
    $subscriber->refresh();
    expect($subscriber->extra_attributes->foo)->toEqual($attributes['extra_attributes']['foo']);
});

it('can append tags when updating a subscriber', function () {
    /** @var Subscriber $subscriber */
    $subscriber = Subscriber::factory()
        ->for(EmailList::factory(), 'emailList')
        ->create()
        ->addTags(['test1', 'test2']);

    $attributes = [
        'tags' => ['test3', 'test4'],
        'append_tags' => true,
    ];

    $this
        ->patchJson(action([SubscribersController::class, 'update'], $subscriber->uuid), $attributes)
        ->assertSuccessful();

    $subscriber->refresh();
    expect($subscriber->tags->pluck('name')->toArray())->toEqual(['test1', 'test2', 'test3', 'test4']);
});

it('can filter on tags', function () {
    $subscribers = Subscriber::factory(3)->create([
        'email_list_id' => test()->emailList->id,
    ]);

    $subscribers->first()->addTag('some tag');

    $response = $this
        ->getJson(action([SubscribersController::class, 'index'], test()->emailList).'?filter[tags]=some+tag')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');

    $response->assertJsonFragment(['email' => $subscribers->first()->email]);
});
