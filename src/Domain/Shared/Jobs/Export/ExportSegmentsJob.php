<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;

class ExportSegmentsJob extends ExportJob
{
    /**
     * @param  array<int>  $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Segments';
    }

    public function execute(): void
    {
        $segments = DB::table(self::getTagSegmentTableName())
            ->select(self::getTagSegmentTableName().'.*', DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'))
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getTagSegmentTableName().'.email_list_id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->get();

        $this->writeFile('segments.csv', $segments);
        $this->addMeta('segments_count', $segments->count());
    }
}
