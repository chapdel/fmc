<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSimpleExcelReaderAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;

class StartSubscriberImportController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(SubscriberImport $subscriberImport)
    {
        $this->authorize('update', $subscriberImport->emailList);

        if ($subscriberImport->status !== SubscriberImportStatus::Draft) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot start a non-draft import.');
        }

        $user = auth()->user();

        $subscriberImport
            ->addMediaFromString($subscriberImport->subscribers_csv)
            ->usingFileName('subscribers.csv')
            ->toMediaCollection('importFile');

        $reader = app(CreateSimpleExcelReaderAction::class)->execute($subscriberImport);

        if (! in_array('email', $reader->getHeaders() ?? []) && ! in_array('Email Address', $reader->getHeaders() ?? [])) {
            $subscriberImport->delete();

            return response()->json([
                'errors' => [
                    'file' => __mc('No header row found. Make sure your first row has at least 1 column with "email"'),
                ],
            ], 422);
        }

        $subscriberImport->update(['subscribers_csv' => null]);

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null, request('sendNotification', true)));

        return $this->respondOk();
    }
}
