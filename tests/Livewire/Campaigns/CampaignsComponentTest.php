<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent;

beforeEach(function () {
    $this->campaign = Campaign::factory()->create();
});

it('can duplicate a campaign', function () {
    test()->authenticate();

    Livewire::test(CampaignsComponent::class)
        ->call('duplicateCampaign', $this->campaign)
        ->assertRedirect(route('mailcoach.campaigns.settings', Campaign::orderByDesc('id')->first()));

    expect(Campaign::count())->toBe(2);

    $duplicatedCampaign = Campaign::orderByDesc('id')->first();

    test()->assertEquals(
        "Duplicate of {$this->campaign->name}",
        $duplicatedCampaign->name
    );

    foreach ([
        'subject',
        'template_id',
        'email_list_id',
        'html',
        'webview_html',
        'segment_class',
        'segment_id',
    ] as $attribute) {
        test()->assertEquals($duplicatedCampaign->$attribute, $this->campaign->$attribute);
    }
});
