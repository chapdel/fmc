<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Exception;
use Illuminate\Foundation\Auth\User;
use OpenSpout\Common\Type;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\CompleteSubscriberImportJob;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscriberJob;
use Spatie\Mailcoach\Domain\Audience\Jobs\UnsubscribeMissingFromImportJob;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImportSubscribersAction
{
    use UsesMailcoachModels;

    protected ?User $user;

    protected string $dateTime;

    protected ?Media $importFile;

    protected SubscriberImport $subscriberImport;

    protected ?TemporaryDirectory $temporaryDirectory;

    public function __construct(
        protected CreateSimpleExcelReaderAction $createSimpleExcelReaderAction
    ) {}

    public function execute(SubscriberImport $subscriberImport, ?User $user = null)
    {
        $this
            ->initialize($subscriberImport, $user)
            ->importSubscribers()
            ->unsubscribeMissing()
            ->cleanupTemporaryFiles();
    }

    protected function initialize(SubscriberImport $subscriberImport, ?User $user): self
    {
        $this->subscriberImport = $subscriberImport;
        $this->user = $user;
        $this->dateTime = $subscriberImport->created_at->format('Y-m-d H:i:s');
        $this->importFile = $subscriberImport->getFirstMedia('importFile');

        return $this;
    }

    protected function importSubscribers(): self
    {
        try {
            $this->subscriberImport->update([
                'status' => SubscriberImportStatus::Importing,
                'imported_subscribers_count' => 0,
            ]);

            $localImportFile = $this->storeLocalImportFile();

            $reader = $this->createSimpleExcelReaderAction->execute($localImportFile);
            $reader
                ->getRows()
                ->each(function (array $values) use (&$totalRows) {
                    $totalRows++;
                    dispatch(new ImportSubscriberJob($this->subscriberImport, $values));
                });

            $this->subscriberImport->update(['status' => SubscriberImportStatus::Importing]);

            dispatch(new CompleteSubscriberImportJob($this->subscriberImport, $totalRows, $this->user));
        } catch (Exception $exception) {
            report($exception);

            $this->subscriberImport->addError(
                __(
                    "Couldn't finish importing subscribers. This error occurred: :error",
                    ['error' => $exception->getMessage()]
                ),
            );

            $this->subscriberImport->update(['status' => SubscriberImportStatus::Failed]);
        }

        return $this;
    }

    protected function unsubscribeMissing(): self
    {
        if (count($this->subscriberImport->fresh()->errors ?? []) || ! $this->subscriberImport['unsubscribe_others']) {
            return $this;
        }

        dispatch(new UnsubscribeMissingFromImportJob($this->subscriberImport));

        return $this;
    }

    protected function cleanupTemporaryFiles(): self
    {
        $this->getTemporaryDirectory()->delete();

        return $this;
    }

    /**
     * Store import file locally and return path to stored file.
     *
     * @return string
     */
    protected function storeLocalImportFile(): string
    {
        $localImportFile = $this->getTemporaryDirectory()
            ->path("import-file-{$this->dateTime}.{$this->importFile->extension}");

        file_put_contents($localImportFile, stream_get_contents($this->importFile->stream()));

        return $localImportFile;
    }

    protected function getTemporaryDirectory(): TemporaryDirectory
    {
        return $this->temporaryDirectory
            ??= new TemporaryDirectory(storage_path('temp'));
    }
}
