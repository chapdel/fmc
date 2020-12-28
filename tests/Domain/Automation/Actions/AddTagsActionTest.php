<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Actions\AddTagsAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

class AddTagsActionTest extends TestCase
{
    private Subscriber $subscriber;

    private AddTagsAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = SubscriberFactory::new()->confirmed()->create();
        $this->action = new AddTagsAction(['some-tag', 'another-tag']);
    }

    /** @test * */
    public function it_continues_after_execution()
    {
        $this->assertTrue($this->action->shouldContinue($this->subscriber));
    }

    /** @test * */
    public function it_wont_halt_after_execution()
    {
        $this->assertFalse($this->action->shouldHalt($this->subscriber));
    }

    /** @test * */
    public function it_adds_tags_to_the_subscriber()
    {
        $this->assertFalse($this->subscriber->hasTag('some-tag'));
        $this->assertFalse($this->subscriber->hasTag('another-tag'));

        $this->action->run($this->subscriber);

        $this->assertTrue($this->subscriber->hasTag('some-tag'));
        $this->assertTrue($this->subscriber->hasTag('another-tag'));
    }
}
