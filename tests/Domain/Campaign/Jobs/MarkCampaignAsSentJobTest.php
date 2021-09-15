<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Jobs\MarkCampaignAsSentJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Snapshots\MatchesSnapshots;

uses(MatchesSnapshots::class);

beforeEach(function () {
    test()->campaign = (new CampaignFactory())
        ->withSubscriberCount(3)
        ->create();

    test()->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    Mail::fake();

    Event::fake();
});

it('marks a campaign as sent and sends an event', function () {
    dispatch(new MarkCampaignAsSentJob(test()->campaign));

    Event::assertDispatched(CampaignSentEvent::class, function (CampaignSentEvent $event) {
        expect($event->campaign->id)->toEqual(test()->campaign->id);

        return true;
    });

    test()->campaign->refresh();
    expect(test()->campaign->status)->toEqual(CampaignStatus::SENT);
});
