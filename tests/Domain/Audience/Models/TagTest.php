<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;

beforeEach(function () {
    test()->subscriber = Subscriber::factory()->create();

    test()->anotherSubscriber = Subscriber::factory()->create();
    test()->anotherSubscriber->email_list_id = test()->subscriber->email_list_id;
    test()->anotherSubscriber->save();

    test()->subscriberOfAnotherEmailList = Subscriber::factory()->create();
});

test('a tag can be added', function () {
    test()->subscriber->addTag('test1');

    assertSubscriberHasTags(['test1']);

    $tagOfEmailList = test()->subscriber->emailList->tags()->first();

    expect($tagOfEmailList->name)->toEqual('test1');

    $tag = Tag::first();

    expect($tag->emailList->id)->toEqual(test()->subscriber->emailList->id);
    expect($tag->subscribers()->first()->uuid)->toEqual(test()->subscriber->uuid);
});

test('a tag can have a type', function () {
    test()->subscriber->addTag('test1', TagType::Mailcoach);

    assertSubscriberHasTags(['test1']);

    $tag = Tag::first();

    expect($tag->type)->toEqual(TagType::Mailcoach);
});

test('multiple tags can be added in one go', function () {
    test()->subscriber->addTags(['test1', 'test2']);

    assertSubscriberHasTags(['test1', 'test2']);
});

it('will not save duplicate tags', function () {
    test()->subscriber->addTags(['test1', 'test2']);
    test()->subscriber->addTags(['test1', 'test2']);

    assertSubscriberHasTags(['test1', 'test2']);
    expect(Tag::all())->toHaveCount(2);
});

test('a tag can be removed', function () {
    test()->subscriber
        ->addTags(['test1', 'test2'])
        ->removeTag('test2');

    assertSubscriberHasTags(['test1']);

    expect(Tag::all())->toHaveCount(2);
});

test('multiple tags can be removed in one go', function () {
    test()->subscriber
        ->addTags(['test1', 'test2', 'test3'])
        ->removeTags(['test1', 'test3']);

    assertSubscriberHasTags(['test2']);
});

test('removing tags fires events', function () {
    Event::fake();

    test()->subscriber
        ->addTags(['test1', 'test2', 'test3'])
        ->removeTags(['test1', 'test3']);

    assertSubscriberHasTags(['test2']);

    Event::assertDispatched(TagRemovedEvent::class, 2);
});

it('can determine if it has a tag', function () {
    expect(test()->subscriber->hasTag('test2'))->toBeFalse();

    test()->subscriber->addTag('test');
    expect(test()->subscriber->hasTag('test2'))->toBeFalse();

    test()->subscriber->addTag('test2');
    expect(test()->subscriber->hasTag('test2'))->toBeTrue();
});

it('can sync tags', function () {
    test()->subscriber->syncTags(['test1', 'test2', 'test3']);

    assertSubscriberHasTags(['test1', 'test2', 'test3']);

    test()->subscriber->syncTags(['test2', 'test4']);

    assertSubscriberHasTags(['test2', 'test4']);

    test()->subscriber->syncTags([]);
    assertSubscriberHasTags([]);
});

test('tags are scoped per email list', function () {
    test()->subscriber->addTag('test1');
    test()->subscriberOfAnotherEmailList->addTag('test1');

    test()->assertDatabaseHas('mailcoach_tags', [
        'email_list_id' => test()->subscriber->emailList->id,
        'name' => 'test1',
    ]);

    test()->assertDatabaseHas('mailcoach_tags', [
        'email_list_id' => test()->subscriberOfAnotherEmailList->emailList->id,
        'name' => 'test1',
    ]);
});

test('subscribers can be retrieved by tag', function () {
    test()->subscriber->syncTags(['testA', 'testB', 'test1', 'test2']);
    test()->anotherSubscriber->syncTags(['test1']);

    $tag1 = Tag::firstWhere('name', '=', 'test1');
    $tag2 = Tag::firstWhere('name', '=', 'test2');

    expect($tag1->subscribers->count())->toBe(2);
    expect($tag2->subscribers->count())->toBe(1);

    expect($tag1->subscribers->pluck('id'))->toContain(test()->subscriber->id);
    expect($tag1->subscribers->pluck('id'))->toContain(test()->anotherSubscriber->id);

    expect($tag2->subscribers->pluck('id'))->toContain(test()->subscriber->id);
});

// Helpers
function assertSubscriberHasTags(array $expectedTagNames)
{
    $actualTags = test()->subscriber->refresh()->tags()->pluck('name')->toArray();
    test()->assertEquals(
        $actualTags,
        $expectedTagNames,
        'Subscriber did not have the expected tags. It currently has '.implode(', ', $actualTags),
    );
}
