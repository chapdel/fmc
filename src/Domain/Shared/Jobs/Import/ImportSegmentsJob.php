<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportSegmentsJob extends ImportJob
{
    /** @var array<int, int> */
    private array $segmentMapping = [];

    private int $total = 0;

    private int $index = 0;

    /** @var array<string, int> */
    private array $emailLists = [];

    private Collection $tags;

    public function name(): string
    {
        return 'Segments';
    }

    public function execute(): void
    {
        $segmentsPath = Storage::disk(config('mailcoach.import_disk'))->path('import/segments.csv');
        $positivePath = Storage::disk(config('mailcoach.import_disk'))->path('import/positive_segment_tags.csv');
        $negativePath = Storage::disk(config('mailcoach.import_disk'))->path('import/negative_segment_tags.csv');

        $this->total = $this->getMeta('segments_count', 0) + $this->getMeta('positive_segment_tags_count', 0) + $this->getMeta('negative_segment_tags_count', 0);
        $this->emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();
        $this->tags = self::getTagClass()::all();

        if (! File::exists($segmentsPath)) {
            return;
        }

        $this->importSegments($segmentsPath);
        $this->importPositiveSegmentTags($positivePath);
        $this->importNegativeSegmentTags($negativePath);
    }

    private function importSegments(string $segmentsPath): void
    {
        $reader = SimpleExcelReader::create($segmentsPath);
        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $this->emailLists[$row['email_list_uuid']];

            $segment = self::getTagSegmentClass()::firstOrCreate(
                ['name' => $row['name'], 'email_list_id' => $row['email_list_id']],
                array_filter(Arr::except($row, ['id', 'email_list_uuid'])),
            );
            $this->segmentMapping[$row['id']] = $segment->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }

    private function importPositiveSegmentTags(string $positivePath): void
    {
        if (! File::exists($positivePath)) {
            return;
        }

        $reader = SimpleExcelReader::create($positivePath);
        foreach ($reader->getRows() as $row) {
            $row['segment_id'] = $this->segmentMapping[$row['segment_id']];
            $row['tag_id'] = $this->tags->where('name', $row['tag_name'])->where('email_list_id', $this->emailLists[$row['email_list_uuid']])->first()->id;

            DB::table('mailcoach_positive_segment_tags')->updateOrInsert(
                array_filter(Arr::except($row, ['id', 'tag_name', 'email_list_uuid'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }

    private function importNegativeSegmentTags(string $negativePath): void
    {
        if (! File::exists($negativePath)) {
            return;
        }

        $reader = SimpleExcelReader::create($negativePath);
        foreach ($reader->getRows() as $row) {
            $row['segment_id'] = $this->segmentMapping[$row['segment_id']];
            $row['tag_id'] = $this->tags->where('name', $row['tag_name'])->where('email_list_id', $this->emailLists[$row['email_list_uuid']])->first()->id;

            DB::table('mailcoach_negative_segment_tags')->updateOrInsert(
                array_filter(Arr::except($row, ['id', 'tag_name', 'email_list_uuid'])),
            );

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }
    }
}
