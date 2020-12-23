<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SubscriberImportRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberImportResource;

class SubscriberImportsController
{
    use RespondsToApiRequests;

    public function index()
    {
        $subscribersImport = SubscriberImport::query()->paginate();

        return SubscriberImportResource::collection($subscribersImport);
    }

    public function show(SubscriberImport $subscriberImport)
    {
        return new SubscriberImportResource($subscriberImport);
    }

    public function store(SubscriberImportRequest $request)
    {
        $attributes = array_merge($request->validated(), ['status' => SubscriberImportStatus::DRAFT]);

        $subscriberImport = SubscriberImport::create($attributes);

        return new SubscriberImportResource($subscriberImport);
    }

    public function update(SubscriberImportRequest $request, SubscriberImport $subscriberImport)
    {
        if ($subscriberImport->status !== SubscriberImportStatus::DRAFT) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot update a non-draft import.');
        }

        $subscriberImport->update($request->validated());

        return new SubscriberImportResource($subscriberImport);
    }

    public function destroy(SubscriberImport $subscriberImport)
    {
        $subscriberImport->delete();

        return $this->respondOk();
    }
}
