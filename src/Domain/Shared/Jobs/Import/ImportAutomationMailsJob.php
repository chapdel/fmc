<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportAutomationMailsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $automationMailMapping = [];

    private int $total = 0;

    private int $index = 0;

    public function name(): string
    {
        return 'Automation Mails';
    }

    public function execute(): void
    {
        $path = Storage::disk(config('mailcoach.import_disk'))->path('import/automation_mails.csv');
        $linksPath = Storage::disk(config('mailcoach.import_disk'))->path('import/automation_mail_links.csv');

        $this->total = $this->getMeta('automation_mails_count', 0) + $this->getMeta('automation_mail_links_count', 0);

        $this->importAutomationMails($path);
        $this->importAutomationMailLinks($linksPath);
    }

    private function importAutomationMails(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        foreach ($reader->getRows() as $row) {
            $automationMail = self::getAutomationMailClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except($row, ['id'])),
            );

            $this->automationMailMapping[$row['id']] = $automationMail->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }

    private function importAutomationMailLinks(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);
        foreach ($reader->getRows() as $row) {
            $row['automation_mail_id'] = $this->automationMailMapping[$row['automation_mail_id']];

            self::getAutomationMailLinkClass()::firstOrCreate(
                ['automation_mail_id' => $row['automation_mail_id'], 'url' => $row['url']],
                array_filter(Arr::except($row, ['id'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }
}
