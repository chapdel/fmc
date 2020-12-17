<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\EnsureTagsExistAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

class EnsureTagExistsActionTest extends TestCase
{
    private Subscriber $subscriber;

    private EnsureTagsExistAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped('TODO: Saving has changed');

        $this->subscriber = SubscriberFactory::new()->confirmed()->create();
        $this->action = new EnsureTagsExistAction('some-tag');
    }

    /** @test * */
    public function it_continues_if_the_subscriber_has_the_tag()
    {
        $this->markTestSkipped('TODO: Action has changed too much');
        $this->assertFalse($this->action->shouldContinue($this->subscriber));

        $this->subscriber->addTag('some-tag');

        $this->assertTrue($this->action->shouldContinue($this->subscriber));
    }

    /** @test * */
    public function it_halts_if_the_action_is_set_to_halt()
    {
        $this->markTestSkipped('TODO: Action has changed too much');
        $action = new EnsureTagsExistAction('some-tag', true);
        $this->assertTrue($action->shouldHalt($this->subscriber));

        $action = new EnsureTagsExistAction('some-tag');
        $this->assertFalse($action->shouldHalt($this->subscriber));

        $this->subscriber->addTag('some-tag');
        $action = new EnsureTagsExistAction('some-tag', true);
        $this->assertFalse($action->shouldHalt($this->subscriber));
    }
}
