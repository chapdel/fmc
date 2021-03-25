<?php

namespace Spatie\Mailcoach\Tests\Domain\Audience\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class UnconfirmedSubscriberCreatedEventTest extends TestCase
{
    /** @test */
    public function it_will_fire_the_unconfirmed_subscribed_created_event_when_a_subscription_still_needs_to_be_confirmed()
    {
        Event::fake(UnconfirmedSubscriberCreatedEvent::class);

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
        $emailList = EmailList::factory()->create([
            'requires_confirmation' => true,
        ]);

        $emailList->subscribe('john@example.com');

        Event::assertDispatched(UnconfirmedSubscriberCreatedEvent::class);
    }
}