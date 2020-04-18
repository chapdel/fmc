<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class UnsubscribedEventTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_someone_unsubscribes()
    {
        Event::fake(UnsubscribedEvent::class);

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscription */
        $subscriber = factory(Subscriber::class)->create();

        $subscriber->unsubscribe();

        Event::assertDispatched(UnsubscribedEvent::class, function (UnsubscribedEvent $event) use ($subscriber) {
            $this->assertEquals($subscriber->id, $event->subscriber->id);

            return true;
        });
    }
}
