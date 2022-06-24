<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class ImportSubscriberTagsJob extends ImportJob
{
    public function name(): string
    {
        return 'Subscriber Tags';
    }

    public function execute(): void
    {
        $files = Finder::create()
            ->in(Storage::disk(config('mailcoach.import_disk'))->path('import'))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'csv' && str_starts_with($file->getFilename(), 'email_list_subscriber_tags'))
            ->sortByName();

        if (! count($files)) {
            return;
        }

        $emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $total = $this->getMeta('email_list_subscriber_tags_count', 0);
        $index = 0;
        foreach ($files as $file) {
            SimpleExcelReader::create($file->getPathname())
                ->getRows()
                ->chunk(5_000)
                ->each(function (LazyCollection $subscriberTags) use ($emailLists, $total, &$index) {
                    $subscribers = self::getSubscriberClass()::whereIn('uuid', $subscriberTags->pluck('subscriber_uuid')->unique())->pluck('id', 'uuid')->toArray();
                    $tags = DB::table(self::getTagTableName())
                        ->select('id', DB::raw('CONCAT(email_list_id, "-", name) as unique_key'))
                        ->whereIn('email_list_id', $emailLists)
                        ->pluck('id', 'unique_key')
                        ->toArray();

                    $existingSubscriberTags = DB::table('mailcoach_email_list_subscriber_tags')
                        ->select(DB::raw('CONCAT(tag_id, "-", subscriber_id) as unique_key'))
                        ->whereIn('tag_id', $tags)
                        ->whereIn('subscriber_id', $subscribers)
                        ->pluck('unique_key', 'unique_key')
                        ->toArray();

                    $newSubscriberTags = $subscriberTags->map(function ($row) use ($existingSubscriberTags, $subscribers, $tags, $emailLists) {
                        $emailListId = $emailLists[$row['email_list_uuid']];
                        $tagId = $tags["{$emailListId}-{$row['tag_name']}"];
                        $subscriberId = $subscribers[$row['subscriber_uuid']];

                        if (isset($existingSubscriberTags["{$tagId}-{$subscriberId}"])) {
                            return null;
                        }

                        return [
                            'subscriber_id' => $subscriberId,
                            'tag_id' => $tagId,
                        ];
                    })->filter()->toArray();

                    DB::table('mailcoach_email_list_subscriber_tags')->insert($newSubscriberTags);

                    $index += 5_000;
                    $this->updateJobProgress($index, $total);
                });
        }
    }
}
