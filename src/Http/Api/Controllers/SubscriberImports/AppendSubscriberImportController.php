<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Spatie\Mailcoach\Http\Api\Requests\AppendSubscriberImportRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberImportResource;
use Spatie\Mailcoach\Models\SubscriberImport;

class AppendSubscriberImportController
{
    public function __invoke(AppendSubscriberImportRequest $request, SubscriberImport $subscriberImport)
    {
        $subscriberImport->update([
            'subscribers_csv' => $subscriberImport->subscribers_csv . PHP_EOL . $request->subscribers_csv,
        ]);

        return new SubscriberImportResource($subscriberImport);
    }
}
