<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Policies\CampaignPolicy;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomCampaignDenyAllPolicy;

class CreateCampaignControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private array $postAttributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->postAttributes = $this->getPostAttributes();
    }

    /** @test */
    public function a_campaign_can_be_created_using_the_api()
    {
        $this
            ->postJson(action([CampaignsController::class, 'store']), $this->postAttributes)
            ->assertSuccessful();

        $campaign = Campaign::first();

        foreach (Arr::except($this->postAttributes, ['type']) as $attributeName => $attributeValue) {
            $this->assertEquals($attributeValue, $campaign->$attributeName);
        }
    }

    /** @test */
    public function access_is_denied_by_custom_authorization_policy()
    {
        app()->bind(CampaignPolicy::class, CustomCampaignDenyAllPolicy::class);

        $this
            ->withExceptionHandling()
            ->postJson(action([CampaignsController::class, 'store']), $this->postAttributes)
            ->assertForbidden();
    }

    private function getPostAttributes(): array
    {
        return [
            'name' => 'name',
            'type' => CampaignStatus::DRAFT,
            'email_list_id' => EmailList::factory()->create()->id,
            'html' => 'html',
            'track_opens' => true,
            'track_clicks' => false,
        ];
    }
}
