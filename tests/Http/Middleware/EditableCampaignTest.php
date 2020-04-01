<?php

namespace Spatie\Mailcoach\Tests\Http\Middleware;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class EditableCampaignTest extends TestCase
{
    /** @test */
    public function it_will_redirect_non_editable_campaigns_to_the_summary()
    {
        Bus::fake();

        $this->authenticate();

        /** @var \Spatie\Mailcoach\Models\Campaign $campaign */
        $campaign = factory(Campaign::class)->create();

        $this
            ->get(route('mailcoach.campaigns.settings', $campaign))
            ->assertSuccessful();

        $campaign->send();

        $this
            ->get(route('mailcoach.campaigns.settings', $campaign))
            ->assertRedirect(route('mailcoach.campaigns.summary', $campaign));
    }
}
