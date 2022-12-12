<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('will retry stuck pending sends', function () {
    Queue::fake();

    $action = app(SendCampaignMailsAction::class);

    $campaign = Campaign::factory()->create([
        'all_sends_dispatched_at' => now()->subMinutes(20),
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sending_job_dispatched_at' => now()->subMinutes(35),
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign->id,
        'sending_job_dispatched_at' => now()->subMinutes(25),
    ]);

    $action->execute($campaign);

    Queue::assertPushed(SendCampaignMailJob::class, 1);
});
