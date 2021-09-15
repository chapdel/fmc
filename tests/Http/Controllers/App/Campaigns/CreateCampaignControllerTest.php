<?php

use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CreateCampaignController;

it('can create a campaign', function () {
    test()->authenticate();

    $this
        ->post(action(CreateCampaignController::class), ['name' => 'my campaign', 'type' => CampaignStatus::DRAFT])
        ->assertRedirect(action([CampaignSettingsController::class, 'edit'], Campaign::first()->id));

    test()->assertDatabaseHas(static::getCampaignTableName(), ['name' => 'my campaign']);
});
