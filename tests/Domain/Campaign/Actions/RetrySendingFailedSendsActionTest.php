<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('updates failed sends and dispatches new jobs', function () {
    Queue::fake();

    $send = Send::factory()->create([
        'failed_at' => now(),
    ]);

    app(RetrySendingFailedSendsAction::class)->execute($send->campaign);

    expect($send->fresh()->failed_at)->toBeNull();

    Queue::assertPushed(SendCampaignMailJob::class);
});
