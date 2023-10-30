<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;

it('updates failed sends to pending again and dispatches new jobs', function () {
    $campaign = CampaignFactory::new()->create();

    $send = Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
        'failed_at' => now(),
        'sent_at' => now(),
    ]);

    expect($send->contentItem->sends()->pending()->count())->toBe(0);
    expect($send->contentItem->sends()->failed()->count())->toBe(1);

    app(RetrySendingFailedSendsAction::class)->execute($send->contentItem->model);

    expect($send->fresh()->failed_at)->toBeNull();
    expect($send->contentItem->sends()->pending()->count())->toBe(1);
    expect($send->contentItem->sends()->undispatched()->count())->toBe(1);
});
