<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignsIndexControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_list_campaign()
    {
        $templates = Campaign::factory(3)->create();

        $this
            ->getJson(action([CampaignsController::class, 'index']))
            ->assertSuccessful()
            ->assertSeeText($templates->first()->name);
    }

    /** @test */
    public function it_can_search_campaigns()
    {
        Campaign::factory()->create([
            'name' => 'one',
        ]);

        Campaign::factory()->create([
            'name' => 'two',
        ]);

        $this
            ->getJson(action([CampaignsController::class, 'index']) . '?filter[search]=two')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'two']);
    }
}
