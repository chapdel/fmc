<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ImportAutomationActionSubscribersJob extends ImportJob
{
    public function name(): string
    {
        return 'Automation Action-Subscribers';
    }

    public function execute(): void
    {
        $files = Finder::create()
            ->in(Storage::disk(config('mailcoach.import_disk'))->path('import'))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'csv' && str_starts_with($file->getFilename(), 'automation_action_subscribers'))
            ->sortByName();

        if (! count($files)) {
            return;
        }

        $actions = self::getAutomationActionClass()::pluck('id', 'uuid')->toArray();

        $total = $this->getMeta('automation_action_subscribers_count', 0);

        $index = 0;
        foreach ($files as $file) {
            $reader = SimpleExcelReader::create($file->getPathname());

            $reader->getRows()->chunk(1000)->each(function (LazyCollection $actionSubscribers) use ($actions, $total, &$index) {
                $subscribers = self::getSubscriberClass()::whereIn('uuid', $actionSubscribers->pluck('subscriber_uuid'))->pluck('id', 'uuid');

                foreach ($actionSubscribers as $row) {
                    $row['action_id'] = $actions[$row['action_uuid']];
                    $row['subscriber_id'] = $subscribers[$row['subscriber_uuid']];

                    if (! self::getActionSubscriberClass()::where([
                        'action_id' => $row['action_id'],
                        'subscriber_id' => $row['subscriber_id'],
                    ])->exists()) {
                        self::getActionSubscriberClass()::insert(array_filter(Arr::except($row, ['id', 'subscriber_uuid', 'action_uuid'])));
                    }

                    $index++;
                    $this->updateJobProgress($index, $total);
                }
            });
        }
    }
}
