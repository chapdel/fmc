<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsIndexController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\UsesApi;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignsIndexControllerTest extends TestCase
{
    use UsesApi;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_list_campaign()
    {
        $templates = factory(Campaign::class, 3)->create();

        $this
            ->getJson(action(CampaignsIndexController::class))
            ->assertSuccessful()
            ->assertSeeText($templates->first()->name);
    }

    /** @test */
    public function it_can_search_campaigns()
    {
        factory(Campaign::class)->create([
            'name' => 'one',
        ]);

        factory(Campaign::class)->create([
            'name' => 'two',
        ]);

        $this
            ->getJson(action(CampaignsIndexController::class) . '?filter[search]=two')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'two']);
    }
}
