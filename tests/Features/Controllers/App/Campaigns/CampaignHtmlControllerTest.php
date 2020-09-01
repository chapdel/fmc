<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\Campaigns;

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignContentController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignHtmlControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_the_html_of_a_campaign()
    {
        $this->authenticate();

        $campaign = Campaign::factory()->create();

        $attributes = [
            'html' => 'updated_html',
        ];

        $this
            ->put(
                action([CampaignContentController::class, 'update'], $campaign->id),
                $attributes
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(action([CampaignContentController::class, 'edit'], $campaign->id));

        $this->assertStringContainsString('updated_html', Campaign::first()->html);
    }
}
