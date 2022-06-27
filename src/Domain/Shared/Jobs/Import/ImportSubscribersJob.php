<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ImportSubscribersJob extends ImportJob
{
    public function name(): string
    {
        return 'Subscribers';
    }

    public function execute(): void
    {
        $files = Finder::create()
            ->in(Storage::disk(config('mailcoach.import_disk'))->path('import'))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'csv' && str_starts_with($file->getFilename(), 'subscribers'))
            ->sortByName();

        if (! count($files)) {
            return;
        }

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $total = $this->getMeta('subscribers_count', 0);
        $index = 0;
        foreach ($files as $file) {
            $reader = SimpleExcelReader::create($file->getPathname());

            $reader->getRows()->chunk(1000)->each(function (LazyCollection $subscribers) use ($emailLists, $total, &$index) {
                $chunkCount = $subscribers->count();
                $existingSubscriberUuids = self::getSubscriberClass()::whereIn('uuid', $subscribers->pluck('uuid'))->pluck('uuid');

                $subscribers->whereNotIn('uuid', $existingSubscriberUuids)->each(function (array $subscriber) use ($emailLists) {
                    $subscriber['email_list_id'] = $emailLists[$subscriber['email_list_uuid']];

                    $columns = Schema::getColumnListing(self::getSubscriberTableName());

                    self::getSubscriberClass()::create(
                        array_filter(Arr::except(Arr::only($subscriber, $columns), ['id']))
                    );
                });

                $index += $chunkCount;
                $this->updateJobProgress($index, $total);
            });
        }
    }
}
