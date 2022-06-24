<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportTransactionalMailTemplatesJob extends ImportJob
{
    public function name(): string
    {
        return 'Transactional Mail Templates';
    }

    public function execute(): void
    {
        $path = Storage::disk(config('mailcoach.import_disk'))->path('import/transactional_mail_templates.csv');

        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        $total = $this->getMeta('transactional_mail_templates_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            self::getTransactionalMailTemplateClass()::firstOrCreate([
                'name' => $row['name'],
                'subject' => $row['subject'],
                'type' => $row['type'],
            ], array_filter(Arr::except($row, ['id'])));

            $this->updateJobProgress($index, $total);
        }
    }
}
