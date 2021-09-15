<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

uses(TestCase::class);

it('fires an event after a campaign has been sent', function () {
    Event::fake(CampaignSentEvent::class);

    $campaign = (new CampaignFactory())
        ->withSubscriberCount(3)
        ->mailable(TestMailcoachMail::class)
        ->create();

    $campaign->content($campaign->contentFromMailable());

    dispatch(new SendCampaignJob($campaign));

    Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) use ($campaign) {
        test()->assertEquals($campaign->id, $event->campaign->id);

        return true;
    });
});
