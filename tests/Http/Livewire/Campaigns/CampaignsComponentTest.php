<?php

use function Pest\Livewire\livewire;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignsComponent;

beforeEach(function () {
    $this->campaign = Campaign::factory()->create();
});

it('can delete a campaign', function () {
    test()->authenticate();

    livewire(CampaignsComponent::class)
        ->call('deleteCampaign', $this->campaign->id);

    expect(Campaign::count())->toBe(0);
});

it('can duplicate a campaign', function () {
    test()->authenticate();

    livewire(CampaignsComponent::class)
        ->call('duplicateCampaign', $this->campaign->id)
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
