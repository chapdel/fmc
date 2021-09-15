<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('will fire an event when a link gets clicked', function () {
    Event::fake();

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $send->registerClick('https://spatie.be');

    test()->assertCount(1, CampaignLink::get());

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
        test()->assertEquals($send->uuid, $event->campaignClick->send->uuid);


        return true;
    });
});

it('will not fire an event when a link gets clicked and click tracking is not enable', function () {
    Event::fake();

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => false]);

    $send->registerClick('https://spatie.be');

    test()->assertCount(0, CampaignLink::get());

    Event::assertNotDispatched(CampaignLinkClickedEvent::class);
});
