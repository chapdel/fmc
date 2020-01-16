<?php

namespace Spatie\Mailcoach\Tests\Models;

use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\Tag;
use Spatie\Mailcoach\Tests\TestCase;

class TagTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Models\Subscriber */
    private Subscriber $subscriber;

    /** @var \Spatie\Mailcoach\Models\Subscriber */
    private Subscriber $subscriberOfAnotherEmailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = factory(Subscriber::class)->create();

        $this->subscriberOfAnotherEmailList = factory(Subscriber::class)->create();
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
