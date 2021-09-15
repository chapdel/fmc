<?php

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Policies\CampaignPolicy;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomCampaignDenyAllPolicy;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->postAttributes = getPostAttributes();
});

test('a campaign can be created using the api', function () {
    $this
        ->postJson(action([CampaignsController::class, 'store']), test()->postAttributes)
        ->assertSuccessful();

    $campaign = Campaign::first();

    foreach (Arr::except(test()->postAttributes, ['type']) as $attributeName => $attributeValue) {
        test()->assertEquals($attributeValue, $campaign->$attributeName);
    }
});

test('access is denied by custom authorization policy', function () {
    app()->bind(CampaignPolicy::class, CustomCampaignDenyAllPolicy::class);

    $this
        ->withExceptionHandling()
        ->postJson(action([CampaignsController::class, 'store']), test()->postAttributes)
        ->assertForbidden();
});

// Helpers
function getPostAttributes(): array
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
