<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Jobs\MarkCampaignAsSentJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

uses(TestCase::class);
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
        test()->assertEquals(test()->campaign->id, $event->campaign->id);

        return true;
    });

    test()->campaign->refresh();
    test()->assertEquals(CampaignStatus::SENT, test()->campaign->status);
});
