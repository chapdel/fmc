<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

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

    test()->assertEquals('test1', $tagOfEmailList->name);

    $tag = Tag::first();

    test()->assertEquals(test()->subscriber->emailList->id, $tag->emailList->id);
    test()->assertEquals(test()->subscriber->uuid, $tag->subscribers()->first()->uuid);
});

test('a tag can have a type', function () {
    test()->subscriber->addTag('test1', TagType::MAILCOACH);

    assertSubscriberHasTags(['test1']);

    $tag = Tag::first();

    test()->assertEquals(TagType::MAILCOACH, $tag->type);
});

test('multiple tags can be added in one go', function () {
    test()->subscriber->addTags(['test1', 'test2']);

    assertSubscriberHasTags(['test1', 'test2']);
});

it('will not save duplicate tags', function () {
    test()->subscriber->addTags(['test1', 'test2']);
    test()->subscriber->addTags(['test1', 'test2']);

    assertSubscriberHasTags(['test1', 'test2']);
    test()->assertCount(2, Tag::all());
});

test('a tag can be removed', function () {
    test()->subscriber
        ->addTags(['test1', 'test2'])
        ->removeTag('test2');

    assertSubscriberHasTags(['test1']);

    test()->assertCount(2, Tag::all());
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
    test()->assertFalse(test()->subscriber->hasTag('test2'));

    test()->subscriber->addTag('test');
    test()->assertFalse(test()->subscriber->hasTag('test2'));

    test()->subscriber->addTag('test2');
    test()->assertTrue(test()->subscriber->hasTag('test2'));
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

    test()->assertSame(2, $tag1->subscribers->count());
    test()->assertSame(1, $tag2->subscribers->count());

    test()->assertContains(test()->subscriber->id, $tag1->subscribers->pluck('id'));
    test()->assertContains(test()->anotherSubscriber->id, $tag1->subscribers->pluck('id'));

    test()->assertContains(test()->subscriber->id, $tag2->subscribers->pluck('id'));
});

// Helpers
function assertSubscriberHasTags(array $expectedTagNames)
{
    $actualTags = test()->subscriber->refresh()->tags()->pluck('name')->toArray();
    test()->assertEquals(
        $actualTags,
        $expectedTagNames,
        'Subscriber did not have the expected tags. It currently has ' . implode(', ', $actualTags),
    );
}
