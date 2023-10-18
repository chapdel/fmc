<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignMailsAction;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('will call the sendcampaignmailsaction for each campaign that has pending sends', function () {
    $campaign1 = Campaign::factory()->create(['status' => CampaignStatus::Sending]);
    $campaign2 = Campaign::factory()->create(['status' => CampaignStatus::Sending]);

    Send::factory()->create([
        'campaign_id' => $campaign1->id,
        'sent_at' => null,
    ]);

    Send::factory()->create([
        'campaign_id' => $campaign2->id,
        'sent_at' => now(),
    ]);

    $this->mock(SendCampaignMailsAction::class)
        ->shouldReceive('execute')->once();

    dispatch_sync(new SendCampaignMailsJob());
});
