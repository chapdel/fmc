<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

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

        return response()->streamDownload(function () use ($emailList) {
            $subscribersQuery = new EmailListSubscribersQuery($emailList);

            $subscriberCsv = SimpleExcelWriter::streamDownload("{$emailList->name} subscribers.csv");

            $header = [
                'email',
                'first_name',
                'last_name',
                'tags',
                'subscribed_at',
                'unsubscribed_at',
            ];

            $attributesQuery = clone $subscribersQuery;
            $attributesQuery->each(function (Subscriber $subscriber) use (&$header) {
                $attributes = array_keys($subscriber->extra_attributes->toArray());
                sort($attributes);

                $header = array_merge($header, $attributes);
            });

            $subscriberCsv->addHeader($header);

            $header = collect($header)->mapWithKeys(fn ($key) => [$key => null])->toArray();

            $subscribersQuery
                ->with(['tags'])
                ->each(function (Subscriber $subscriber) use ($subscriberCsv, $header) {
                    $this->resetMaximumExecutionTime();

                    $subscriberCsv->addRow(array_merge($header, $subscriber->toExportRow()));

                    flush();
                });

            $subscriberCsv->close();
        }, "{$emailList->name} subscribers.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function resetMaximumExecutionTime(): void
    {
        $maximumExecutionTime = (int) ini_get('max_execution_time');

        set_time_limit($maximumExecutionTime);
    }
}
