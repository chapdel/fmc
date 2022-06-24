<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportTemplatesJob extends ImportJob
{
    public function name(): string
    {
        return 'Templates';
    }

    public function execute(): void
    {
        $path = Storage::disk(config('mailcoach.import_disk'))->path('import/templates.csv');

        if (! File::exists($path)) {
            return;
        }

        $reader = SimpleExcelReader::create($path);

        $total = $this->getMeta('templates_count', 0);
        foreach ($reader->getRows() as $index => $row) {
            self::getTemplateClass()::firstOrCreate(
                ['name' => $row['name'], 'html' => $row['html']],
                Arr::except($row, ['id'])
            );

            $this->updateJobProgress($index, $total);
        }
    }
}
