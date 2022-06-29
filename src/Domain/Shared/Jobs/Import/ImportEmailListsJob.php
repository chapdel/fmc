<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportEmailListsJob extends ImportJob
{
    public function name(): string
    {
        return 'Email lists';
    }

    public function execute(): void
    {
        if (! $this->importDisk->exists('import/email_lists.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/email_lists.csv', $this->importDisk->readStream('import/email_lists.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/email_lists.csv'));

        $total = $this->getMeta('email_lists_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            self::getEmailListClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except($row, ['id'])),
            );

            $this->updateJobProgress($index, $total);
        }

        $this->tmpDisk->delete('tmp/email_lists.csv');
    }
}
