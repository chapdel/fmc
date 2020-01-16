<?php

namespace Spatie\Mailcoach\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignMailSentEventTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_the_mail_is_sent()
    {
        Event::fake(CampaignMailSentEvent::class);

        $send = factory(Send::class)->create();

        dispatch(new SendMailJob($send));

        Event::assertDispatched(CampaignMailSentEvent::class, function (CampaignMailSentEvent $event) use ($send) {
            $this->assertEquals($send->uuid, $event->send->uuid);

            return true;
        });
    }
}
