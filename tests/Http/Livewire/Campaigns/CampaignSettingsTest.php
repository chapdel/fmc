<?php

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettings;

it('can update the settings of a campaign', function () {
    test()->withoutExceptionHandling();

    test()->authenticate();

    $campaign = Campaign::create(['name' => 'my campaign']);

    \Livewire\Livewire::test(CampaignSettings::class, ['campaign' => $campaign])
        ->set('campaign.name', 'updated name')
        ->set('campaign.subject', 'my subject')
        ->set('campaign.email_list_id', EmailList::factory()->create()->id)
        ->set('campaign.track_opens', true)
        ->set('campaign.track_clicks', true)
        ->set('campaign.utm_tags', true)
        ->set('segment', 'entire_list')
        ->call('save')
        ->assertHasNoErrors();

    test()->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'updated name',
        'subject' => 'my subject',
        'track_opens' => true,
        'track_clicks' => true,
        'utm_tags' => true,
    ]);
});
