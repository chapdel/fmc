<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;

it('fires an event after a campaign has been sent', function () {
    Event::fake(CampaignSentEvent::class);

    $campaign = (new CampaignFactory())
        ->withSubscriberCount(3)
        ->mailable(TestMailcoachMail::class)
        ->create();

    $campaign->content($campaign->contentFromMailable());

    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');
    Artisan::call('mailcoach:send-campaign-mails');
    Artisan::call('mailcoach:send-scheduled-campaigns');

    Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) use ($campaign) {
        expect($event->campaign->id)->toEqual($campaign->id);

        return true;
    });
});
