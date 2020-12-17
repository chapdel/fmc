<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\Campaigns;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignSettingsControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_the_settings_of_a_campaign()
    {
        $this->withoutExceptionHandling();

        $this->authenticate();

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

        $this->assertDatabaseHas('mailcoach_campaigns', Arr::except($attributes, ['segment']));
    }
}
