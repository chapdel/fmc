<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Events\ComplaintRegisteredEvent;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class ComplaintRegisteredEventTest extends TestCase
{
    /** @test */
    public function it_will_send_an_event_after_a_complaint_has_been_registered()
    {
        Event::fake();

        /** @var Send $send */
        $send = factory(Send::class)->create();

        $send->registerComplaint();

        Event::assertDispatched(ComplaintRegisteredEvent::class);
    }
}
