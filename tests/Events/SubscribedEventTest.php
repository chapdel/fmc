<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Events\SubscribedEvent;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class SubscribedEventTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake(SubscribedEvent::class);
    }

    /** @test */
    public function it_fires_an_event_when_someone_subscribes()
    {
        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create([
            'requires_confirmation' => false,
        ]);

        $emailList->subscribe('john@example.com');

        Event::assertDispatched(SubscribedEvent::class, function (SubscribedEvent $event) {
            $this->assertEquals('john@example.com', $event->subscriber->email);

            return true;
        });
    }

    /** @test */
    public function it_will_not_fire_the_subscription_event_when_a_subscription_still_needs_to_be_confirmed()
    {
        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create([
            'requires_confirmation' => true,
        ]);

        $emailList->subscribe('john@example.com');

        Event::assertNotDispatched(SubscribedEvent::class);
    }

    /** @test */
    public function it_will_fire_the_subscribe_event_when_a_subscription_is_confirmed()
    {
        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create([
            'requires_confirmation' => true,
        ]);

        $subcriber = $emailList->subscribe('john@example.com');

        Event::assertNotDispatched(SubscribedEvent::class);

        $subcriber->confirm();

        Event::assertDispatched(SubscribedEvent::class, function (SubscribedEvent $event) {
            $this->assertEquals('john@example.com', $event->subscriber->email);

            return true;
        });
    }
}
