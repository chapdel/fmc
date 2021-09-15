<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendCampaignTestController;
use Spatie\Mailcoach\Tests\TestCase;


beforeEach(function () {
    test()->authenticate();

    test()->campaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
    ]);

    Bus::fake();
});

it('can send test mails', function () {
    test()->post(
        action(SendCampaignTestController::class, test()->campaign),
        ['emails' => 'test@example.com']
    );

    Bus::assertDispatched(SendCampaignTestJob::class);
});
