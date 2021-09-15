<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;

it('will fire an event when a link gets clicked', function () {
    Event::fake();

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $send->registerClick('https://spatie.be');

    expect(CampaignLink::get())->toHaveCount(1);

    test()->assertDatabaseHas('mailcoach_campaign_links', [
        'campaign_id' => $send->campaign->id,
        'url' => 'https://spatie.be',
    ]);

    test()->assertDatabaseHas('mailcoach_campaign_clicks', [
        'send_id' => $send->id,
        'campaign_link_id' => CampaignLink::first()->id,
        'subscriber_id' => $send->subscriber->id,
    ]);

    Event::assertDispatched(CampaignLinkClickedEvent::class, function (CampaignLinkClickedEvent $event) use ($send) {
        expect($event->campaignClick->send->uuid)->toEqual($send->uuid);


        return true;
    });
});

it('will not fire an event when a link gets clicked and click tracking is not enable', function () {
    Event::fake();

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => false]);

    $send->registerClick('https://spatie.be');

    expect(CampaignLink::get())->toHaveCount(0);

    Event::assertNotDispatched(CampaignLinkClickedEvent::class);
});
