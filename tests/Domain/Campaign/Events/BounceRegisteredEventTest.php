<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\BounceRegisteredEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class BounceRegisteredEventTest extends TestCase
{
    /** @test */
    public function it_will_send_an_event_after_a_bounce_has_been_registered()
    {
        Event::fake();

        /** @var Send $send */
        $send = SendFactory::new()->create();

        $send->registerBounce();

        Event::assertDispatched(BounceRegisteredEvent::class);
    }
}
