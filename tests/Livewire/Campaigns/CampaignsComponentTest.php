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

    expect($duplicatedCampaign->name)->toBe("Duplicate of {$this->campaign->name}");
    expect($duplicatedCampaign->contentItem->id)->not()->toBe($this->campaign->contentItem->id);

    foreach ([
        'email_list_id',
        'segment_class',
        'segment_id',
    ] as $attribute) {
        expect($duplicatedCampaign->$attribute)->toBe($this->campaign->$attribute);
    }

    foreach ([
        'subject',
        'template_id',
        'html',
        'webview_html',
    ] as $attribute) {
        expect($duplicatedCampaign->contentItem->$attribute)->toBe($this->campaign->contentItem->$attribute);
    }
});
