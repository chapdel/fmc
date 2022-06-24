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
        $path = Storage::disk(config('mailcoach.import_disk'))->path('import/email_lists.csv');

        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        $total = $this->getMeta('email_lists_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            self::getEmailListClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except($row, ['id'])),
            );

            $this->updateJobProgress($index, $total);
        }
    }
}
