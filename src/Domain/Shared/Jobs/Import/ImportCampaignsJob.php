<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportCampaignsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $campaignMapping = [];

    private int $total = 0;

    private int $index = 0;

    public function name(): string
    {
        return 'Campaigns';
    }

    public function execute(): void
    {
        $path = Storage::disk(config('mailcoach.import_disk'))->path('import/campaigns.csv');
        $linksPath = Storage::disk(config('mailcoach.import_disk'))->path('import/campaign_links.csv');

        $this->total = $this->getMeta('campaigns_count', 0) + $this->getMeta('campaign_links_count', 0);

        $this->importCampaigns($path);
        $this->importCampaignLinks($linksPath);
    }

    private function importCampaigns(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $emailLists[$row['email_list_uuid']];
            $row['segment_id'] = self::getTagSegmentClass()::where('name', $row['segment_name'])->where('email_list_id', $row['email_list_id'])->first()?->id;

            $campaign = self::getCampaignClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except($row, ['id', 'email_list_uuid', 'segment_name'])),
            );

            $this->campaignMapping[$row['id']] = $campaign->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }

    private function importCampaignLinks(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        foreach ($reader->getRows() as $campaignLinkData) {
            $campaignLinkData['campaign_id'] = $this->campaignMapping[$campaignLinkData['campaign_id']];

            self::getCampaignLinkClass()::firstOrCreate(
                ['campaign_id' => $campaignLinkData['campaign_id'], 'url' => $campaignLinkData['url']],
                array_filter(Arr::except($campaignLinkData, ['id'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }
}
