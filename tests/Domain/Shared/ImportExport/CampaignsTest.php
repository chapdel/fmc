<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportCampaignsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportCampaignsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import campaigns', function () {
    $campaigns = Campaign::factory(10)->create();
    CampaignLink::factory()->create(['campaign_id' => $campaigns->first()->id]);

    (new ExportCampaignsJob('import', $campaigns->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/campaigns.csv'))->toBeTrue();
    expect($this->disk->exists('import/campaign_links.csv'))->toBeTrue();

    Campaign::query()->delete();
    CampaignLink::query()->delete();

    expect(Campaign::count())->toBe(0);
    expect(CampaignLink::count())->toBe(0);

    (new ImportCampaignsJob())->handle();
    (new ImportCampaignsJob())->handle(); // Don't import duplicates

    expect(Campaign::count())->toBe(10);
    expect(CampaignLink::count())->toBe(1);
});
