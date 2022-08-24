<?php

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Policies\CampaignPolicy;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestClasses\CustomCampaignDenyAllPolicy;

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

    foreach (Arr::except(test()->postAttributes, ['type', 'email_list_uuid']) as $attributeName => $attributeValue) {
        test()->assertEquals($attributeValue, $campaign->$attributeName);
    }
    test()->assertEquals(test()->postAttributes['email_list_uuid'], $campaign->emailList->uuid);
});

it('can be created with a tagsegment', function () {
    $tagsegment = TagSegment::factory()->create();

    $this
        ->postJson(action([CampaignsController::class, 'store']), array_merge(test()->postAttributes, [
            'segment_uuid' => $tagsegment->uuid,
        ]))
        ->assertSuccessful();

    $campaign = Campaign::first();

    test()->assertEquals(TagSegment::class, $campaign->segment_class);
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
        'type' => CampaignStatus::Draft,
        'email_list_uuid' => EmailList::factory()->create()->uuid,
        'html' => 'html',
    ];
}
