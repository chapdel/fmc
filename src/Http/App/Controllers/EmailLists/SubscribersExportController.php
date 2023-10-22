<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SubscribersExportController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        ini_set('max_execution_time', '0');

        $filename = str($emailList->name)->slug();

        return response()->streamDownload(function () use ($emailList, $filename) {
            $subscribersQuery = new EmailListSubscribersQuery($emailList);

            $subscriberCsv = SimpleExcelWriter::streamDownload("{$filename} subscribers.csv");

            $header = [
                'email' => null,
                'first_name' => null,
                'last_name' => null,
                'tags' => null,
                'subscribed_at' => null,
                'unsubscribed_at' => null,
            ];

            $attributesQuery = clone $subscribersQuery;
            $attributesQuery
                ->selectRaw(DB::raw('DISTINCT JSON_KEYS(extra_attributes) as extra_attributes'))
                ->reorder()
                ->groupBy('extra_attributes')
                ->pluck('extra_attributes')
                ->each(function ($subscriber) use (&$header) {
                    $attributes = collect($subscriber)->mapWithKeys(fn ($key) => [$key => null])->toArray();
                    ksort($attributes);

                    $header = array_merge($header, $attributes);
                });

            $subscriberCsv->addHeader(array_unique(array_keys($header)));

            $subscribersQuery
                ->with(['tags'])
                ->each(function (Subscriber $subscriber) use ($subscriberCsv, $header) {
                    $this->resetMaximumExecutionTime();

                    $subscriberCsv->addRow(array_merge($header, $subscriber->toExportRow()));

                    flush();
                });

            $subscriberCsv->close();
        }, "{$filename} subscribers.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function resetMaximumExecutionTime(): void
    {
        $maximumExecutionTime = (int) ini_get('max_execution_time');

        set_time_limit($maximumExecutionTime);
    }
}
