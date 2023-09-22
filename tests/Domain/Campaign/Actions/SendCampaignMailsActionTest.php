<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\SendCampaignTimeLimitApproaching;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('will retry stuck pending sends', function () {
    Queue::fake();

    $action = app(SendCampaignMailsAction::class);

    $campaign = Campaign::factory()->create([
        'all_sends_dispatched_at' => now()->subMinutes(20),
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 10_000,
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sending_job_dispatched_at' => now()->subMinutes(35),
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sending_job_dispatched_at' => now()->subMinutes(25),
    ]);

    $action->execute($campaign);

    Queue::assertPushed(SendCampaignMailJob::class, 1);
});

it('will halt when running out of time', function () {
    Queue::fake();
    $action = app(SendCampaignMailsAction::class);

    $campaign = Campaign::factory()->create([
        'all_sends_dispatched_at' => now()->subMinutes(20),
    ]);

    $campaign->contentItem->update([
        'sent_to_number_of_subscribers' => 10_000,
    ]);

    config()->set("mail.mailers.{$campaign->getMailerKey()}.mails_per_timespan", 1);
    config()->set("mail.mailers.{$campaign->getMailerKey()}.timespan_in_seconds", 120);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sending_job_dispatched_at' => now()->subHours(4),
    ]);

    Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'sending_job_dispatched_at' => now()->subHours(4),
    ]);

    $exception = false;
    try {
        $action->execute($campaign, now()->addSeconds(20));
    } catch (SendCampaignTimeLimitApproaching) {
        $exception = true;
    }

    expect($exception)->toBeTrue();
    Queue::assertPushed(SendCampaignMailJob::class, 1);
});
