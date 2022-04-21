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

it('will use default tracking settings', function () {
    test()->authenticate();

    $this
        ->post(action(CreateCampaignController::class), ['name' => 'my campaign', 'type' => CampaignStatus::DRAFT])
        ->assertRedirect(action([CampaignSettingsController::class, 'edit'], Campaign::first()->id));

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'my campaign',
        'track_opens' => 0,
        'track_clicks' => 0,
        'utm_tags' => 1,
    ]);

    config()->set('mailcoach.campaigns.default_settings.track_opens', true);
    config()->set('mailcoach.campaigns.default_settings.track_clicks', true);
    config()->set('mailcoach.campaigns.default_settings.utm_tags', false);

    $this->post(action(CreateCampaignController::class), ['name' => 'another campaign', 'type' => CampaignStatus::DRAFT]);

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'another campaign',
        'track_opens' => 1,
        'track_clicks' => 1,
        'utm_tags' => 0,
    ]);
});
