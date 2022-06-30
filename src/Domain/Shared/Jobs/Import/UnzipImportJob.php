<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

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
        $this->tmpDisk->writeStream('import.zip', $this->importDisk->readStream($this->path));

        $zip = new ZipArchive();
        $zip->open($this->tmpDisk->path('import.zip'));
        $zip->extractTo($this->tmpDisk->path('tmp/import'));
        $zip->close();

        $this->importDisk->deleteDirectory('import');
        $this->importDisk->makeDirectory('import');

        $files = $this->tmpDisk->allFiles('tmp/import');
        foreach ($files as $file) {
            $this->importDisk->writeStream(str_replace('tmp/', '', $file), $this->tmpDisk->readStream($file));
        }
        $this->tmpDisk->deleteDirectory('tmp/import');

        $this->importDisk->delete($this->path);
    }
}
