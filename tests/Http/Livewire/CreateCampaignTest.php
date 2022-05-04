<?php

use function Pest\Livewire\livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Livewire\CreateCampaign;

beforeEach(function () {
    EmailList::factory()->create();
    test()->authenticate();
});

it('can create a campaign', function () {
    livewire(CreateCampaign::class)
        ->set('name', 'My campaign')
        ->call('saveCampaign')
        ->assertRedirect(action([CampaignSettingsController::class, 'edit'], Campaign::first()->id));

    test()->assertDatabaseHas(static::getCampaignTableName(), ['name' => 'My campaign']);
});

it('will use default tracking settings', function () {
    livewire(CreateCampaign::class)
        ->set('name', 'my campaign')
        ->call('saveCampaign');

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'my campaign',
        'track_opens' => 0,
        'track_clicks' => 0,
        'utm_tags' => 1,
    ]);

    config()->set('mailcoach.campaigns.default_settings.track_opens', true);
    config()->set('mailcoach.campaigns.default_settings.track_clicks', true);
    config()->set('mailcoach.campaigns.default_settings.utm_tags', false);

    livewire(CreateCampaign::class)
        ->set('name', 'another campaign')
        ->call('saveCampaign');

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'another campaign',
        'track_opens' => 1,
        'track_clicks' => 1,
        'utm_tags' => 0,
    ]);
});
