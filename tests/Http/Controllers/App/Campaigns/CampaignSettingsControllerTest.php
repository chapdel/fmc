<?php

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;

it('can update the settings of a campaign', function () {
    test()->withoutExceptionHandling();

    test()->authenticate();

    $campaign = Campaign::create(['name' => 'my campaign']);

    $attributes = [
        'name' => 'updated name',
        'subject' => 'my subject',
        'email_list_id' => EmailList::factory()->create()->id,
        'track_opens' => true,
        'track_clicks' => true,
        'segment' => 'entire_list',
    ];

    $this
        ->put(
            action([CampaignSettingsController::class, 'update'], $campaign->id),
            $attributes
        )
        ->assertSessionHasNoErrors()
        ->assertRedirect(action([CampaignSettingsController::class, 'edit'], $campaign->id));

    test()->assertDatabaseHas(static::getCampaignTableName(), Arr::except($attributes, ['segment']));
});
