<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\PublicApi;

use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\Front\Controllers\CampaignWebviewController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignWebviewControllerTest extends TestCase
{
    private Campaign $campaign;

    private string $webviewUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = factory(Campaign::class)->create([
            'webview_html' => 'my webview html',
        ]);

        $this->campaign->markAsSent(1);

        $this->webviewUrl = action(CampaignWebviewController::class, $this->campaign->uuid);
    }

    /** @test */
    public function it_can_display_the_webview_for_a_campaign()
    {
        $this
            ->get($this->webviewUrl)
            ->assertSuccessful()
            ->assertSee('my webview html');
    }

    /** @test */
    public function it_will_not_display_a_webview_for_a_campaign_that_has_not_been_sent()
    {
        $this->withExceptionHandling();

        $this->campaign->update(['status' => CampaignStatus::DRAFT]);

        $this
            ->get($this->webviewUrl)
            ->assertStatus(404);
    }
}
