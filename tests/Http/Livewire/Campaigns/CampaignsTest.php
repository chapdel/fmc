<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->campaign = Campaign::factory()->create();
});

it('can delete a campaign', function () {
    test()->authenticate();

    livewire(Campaigns::class)
        ->call('deleteCampaign', $this->campaign->id);

    expect(Campaign::count())->toBe(0);
});
