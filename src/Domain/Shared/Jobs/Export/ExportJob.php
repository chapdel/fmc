<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Throwable;

abstract class ExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    protected Filesystem $exportDisk;

    protected Filesystem $tmpDisk;

    abstract public function name(): string;

    abstract public function execute(): void;

    protected function writeFile(string $name, Collection $data): void
    {
        $this->tmpDisk->makeDirectory($this->path);

        $filePath = $this->path.DIRECTORY_SEPARATOR.$name;

        $writer = SimpleExcelWriter::create($this->tmpDisk->path($filePath));
        $writer->addRows($data->map(fn (object $data) => (array) $data));
        $writer->close();

        $this->exportDisk->put($filePath, $this->tmpDisk->get($filePath));
    }

    protected function addMeta(string $key, mixed $value): void
    {
        $jsonPath = $this->path.DIRECTORY_SEPARATOR.'meta.json';

        $meta = [];
        if ($this->exportDisk->exists($jsonPath)) {
            $meta = json_decode($this->exportDisk->get($jsonPath), true);
        }

        $meta[$key] = $value;

        $this->exportDisk->put($jsonPath, json_encode($meta));
    }

    protected function jobStarted(): void
    {
        $steps = Cache::get('export-status', []);

        $steps[$this->name()] = [
            'started' => true,
            'finished' => false,
            'error' => '',
        ];

        Cache::put('export-status', $steps);
    }

    protected function jobFinished(): void
    {
        $steps = Cache::get('export-status', []);

        $steps[$this->name()]['finished'] = true;

        Cache::put('export-status', $steps);
    }

    protected function jobFailed(string $exceptionMessage): void
    {
        $steps = Cache::get('export-status', []);

        $steps[$this->name()]['error'] = $exceptionMessage;

        Cache::put('export-status', $steps);
    }

    public function handle(): void
    {
        $this->exportDisk = Storage::disk(config('mailcoach.export_disk'));
        $this->tmpDisk = Storage::disk(config('mailcoach.tmp_disk'));

        $this->jobStarted();

        $this->execute();

        $this->jobFinished();
    }

    public function failed(Throwable $exception)
    {
        $this->jobFailed($exception->getMessage());
    }
}
