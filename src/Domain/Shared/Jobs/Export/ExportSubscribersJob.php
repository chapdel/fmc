<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExportSubscribersJob extends ExportJob
{
    /**
     * @param string $path
     * @param array<int> $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Subscribers';
    }

    public function execute(): void
    {
        $subscribersCount = 0;

        DB::table(self::getSubscriberTableName())
            ->select(self::getSubscriberTableName(). '.*', DB::raw(self::getEmailListTableName() . '.uuid as email_list_uuid'))
            ->join(self::getEmailListTableName(), self::getEmailListTableName() . '.id', self::getSubscriberTableName().'.email_list_id')
            ->orderBy('id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->chunk(10_000, function (Collection $subscribers, $index) use (&$subscribersCount) {
                $subscribersCount += $subscribers->count();
                $this->writeFile("subscribers-{$index}.csv", $subscribers);
            });

        $this->addMeta('subscribers_count', $subscribersCount);
    }
}
