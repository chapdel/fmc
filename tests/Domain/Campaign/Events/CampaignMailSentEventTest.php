<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;

it('will fire an event when the mail is sent', function () {
    Event::fake(CampaignMailSentEvent::class);

    $send = SendFactory::new()->create();
    $send->subscriber->update(['email_list_id' => $send->campaign->email_list_id]);

    dispatch(new SendCampaignMailJob($send));

    Event::assertDispatched(CampaignMailSentEvent::class, function (CampaignMailSentEvent $event) use ($send) {
        expect($event->send->uuid)->toEqual($send->uuid);

        return true;
    });
});
