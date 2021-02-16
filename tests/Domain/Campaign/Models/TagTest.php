<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Models;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Tests\TestCase;

class TagTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber */
    private Subscriber $subscriber;

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber */
    private Subscriber $anotherSubscriber;

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber */
    private Subscriber $subscriberOfAnotherEmailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = Subscriber::factory()->create();

        $this->anotherSubscriber = Subscriber::factory()->create();
        $this->anotherSubscriber->email_list_id = $this->subscriber->email_list_id;
        $this->anotherSubscriber->save();

        $this->subscriberOfAnotherEmailList = Subscriber::factory()->create();
    }

    /** @test */
    public function a_tag_can_be_added()
    {
        $this->subscriber->addTag('test1');

        $this->assertSubscriberHasTags(['test1']);

        $tagOfEmailList = $this->subscriber->emailList->tags()->first();

        $this->assertEquals('test1', $tagOfEmailList->name);

        $tag = Tag::first();

        $this->assertEquals($this->subscriber->emailList->id, $tag->emailList->id);
        $this->assertEquals($this->subscriber->uuid, $tag->subscribers()->first()->uuid);
    }

    /** @test * */
    public function a_tag_can_have_a_type()
    {
        $this->subscriber->addTag('test1', TagType::MAILCOACH);

        $this->assertSubscriberHasTags(['test1']);

        $tag = Tag::first();

        $this->assertEquals(TagType::MAILCOACH, $tag->type);
    }

    /** @test */
    public function multiple_tags_can_be_added_in_one_go()
    {
        $this->subscriber->addTags(['test1', 'test2']);

        $this->assertSubscriberHasTags(['test1', 'test2']);
    }

    /** @test */
    public function it_will_not_save_duplicate_tags()
    {
        $this->subscriber->addTags(['test1', 'test2']);
        $this->subscriber->addTags(['test1', 'test2']);

        $this->assertSubscriberHasTags(['test1', 'test2']);
        $this->assertCount(2, Tag::all());
    }

    /** @test */
    public function a_tag_can_be_removed()
    {
        $this->subscriber
            ->addTags(['test1', 'test2'])
            ->removeTag('test2');

        $this->assertSubscriberHasTags(['test1']);

        $this->assertCount(2, Tag::all());
    }

    /** @test */
    public function multiple_tags_can_be_removed_in_one_go()
    {
        $this->subscriber
            ->addTags(['test1', 'test2', 'test3'])
            ->removeTags(['test1', 'test3']);

        $this->assertSubscriberHasTags(['test2']);
    }

    /** @test */
    public function removing_tags_fires_events()
    {
        Event::fake();

        $this->subscriber
            ->addTags(['test1', 'test2', 'test3'])
            ->removeTags(['test1', 'test3']);

        $this->assertSubscriberHasTags(['test2']);

        Event::assertDispatched(TagRemovedEvent::class, 2);
    }

    /** @test */
    public function it_can_determine_if_it_has_a_tag()
    {
        $this->assertFalse($this->subscriber->hasTag('test2'));

        $this->subscriber->addTag('test');
        $this->assertFalse($this->subscriber->hasTag('test2'));

        $this->subscriber->addTag('test2');
        $this->assertTrue($this->subscriber->hasTag('test2'));
    }

    /** @test */
    public function it_can_sync_tags()
    {
        $this->subscriber->syncTags(['test1', 'test2', 'test3']);

        $this->assertSubscriberHasTags(['test1', 'test2', 'test3']);

        $this->subscriber->syncTags(['test2', 'test4']);

        $this->assertSubscriberHasTags(['test2', 'test4']);

        $this->subscriber->syncTags([]);
        $this->assertSubscriberHasTags([]);
    }

    /** @test */
    public function tags_are_scoped_per_emailList()
    {
        $this->subscriber->addTag('test1');
        $this->subscriberOfAnotherEmailList->addTag('test1');

        $this->assertDatabaseHas('mailcoach_tags', [
            'email_list_id' => $this->subscriber->id,
            'name' => 'test1',
        ]);

        $this->assertDatabaseHas('mailcoach_tags', [
            'email_list_id' => $this->subscriberOfAnotherEmailList->id,
            'name' => 'test1',
        ]);
    }

    /** @test */
    public function subscribers_can_be_retrieved_by_tag()
    {
        $this->subscriber->syncTags(['testA', 'testB', 'test1', 'test2']);
        $this->anotherSubscriber->syncTags(['test1']);

        $tag1 = Tag::firstWhere('name', '=', 'test1');
        $tag2 = Tag::firstWhere('name', '=', 'test2');

        $this->assertSame(2, $tag1->subscribers->count());
        $this->assertSame(1, $tag2->subscribers->count());

        $this->assertContains($this->subscriber->id, $tag1->subscribers->pluck('id'));
        $this->assertContains($this->anotherSubscriber->id, $tag1->subscribers->pluck('id'));

        $this->assertContains($this->subscriber->id, $tag2->subscribers->pluck('id'));
    }

    protected function assertSubscriberHasTags(array $expectedTagNames)
    {
        $actualTags = $this->subscriber->refresh()->tags()->pluck('name')->toArray();
        $this->assertEquals(
            $actualTags,
            $expectedTagNames,
            'Subscriber did not have the expected tags. It currently has ' . implode(', ', $actualTags),
        );
    }
}
