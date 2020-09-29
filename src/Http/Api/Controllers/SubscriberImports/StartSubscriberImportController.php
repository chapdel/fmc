<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Response;
use Spatie\Mailcoach\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Models\SubscriberImport;

class StartSubscriberImportController
{
    use RespondsToApiRequests;

    public function __invoke(SubscriberImport $subscriberImport)
    {
        if ($subscriberImport->status !== SubscriberImportStatus::DRAFT) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot start a non-draft import.');
        }

        $user = auth()->user();

        $subscriberImport
            ->addMediaFromString($subscriberImport->subscribers_csv)
            ->usingFileName('subscribers.csv')
            ->toMediaCollection('importFile');

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null));

        return $this->respondOk();
    }
}