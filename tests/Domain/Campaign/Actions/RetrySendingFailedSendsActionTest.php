<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\RetrySendingFailedSendsAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('updates failed sends to pending again and dispatches new jobs', function () {
    $send = Send::factory()->create([
        'failed_at' => now(),
        'sent_at' => now(),
    ]);

    expect($send->campaign->sends()->pending()->count())->toBe(0);

    app(RetrySendingFailedSendsAction::class)->execute($send->campaign);

    expect($send->fresh()->failed_at)->toBeNull();
    expect($send->campaign->sends()->pending()->count())->toBe(1);
    expect($send->campaign->sends()->undispatched()->count())->toBe(1);
});
