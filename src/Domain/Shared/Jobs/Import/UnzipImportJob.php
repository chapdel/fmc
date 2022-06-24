<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class UnzipImportJob extends ImportJob
{
    public function __construct(public string $path)
    {
    }

    public function name(): string
    {
        return 'Unzip file';
    }

    public function execute(): void
    {
        $disk = Storage::disk(config('mailcoach.import_disk'));

        $zip = new ZipArchive();
        $zip->open($disk->path($this->path));
        $zip->extractTo($disk->path('import/'));
        $zip->close();

        Storage::disk(config('mailcoach.import_disk'))->delete($this->path);
    }
}
