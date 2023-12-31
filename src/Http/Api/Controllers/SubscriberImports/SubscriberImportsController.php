<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SubscriberImportRequest;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberImportIndexResource;
use Spatie\Mailcoach\Http\Api\Resources\SubscriberImportResource;

class SubscriberImportsController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index()
    {
        $this->authorize('viewAny', self::getEmailListClass());

        $subscribersImport = self::getSubscriberImportClass()::query()
            ->select([
                'uuid',
                'status',
                'subscribe_unsubscribed',
                'unsubscribe_others',
                'replace_tags',
                'imported_subscribers_count',
                'errors',
                'email_list_id',
            ])
            ->with(['emailList'])
            ->when(request('filter.email_list_uuid'), function (Builder $query) {
                $query->whereHas('emailList', function (Builder $query) {
                    $query->where('uuid', request('filter.email_list_uuid'));
                });
            })
            ->paginate();

        return SubscriberImportIndexResource::collection($subscribersImport);
    }

    public function show(SubscriberImport $subscriberImport)
    {
        $this->authorize('view', $subscriberImport->emailList);

        return new SubscriberImportResource($subscriberImport);
    }

    public function store(SubscriberImportRequest $request)
    {
        $this->authorize('update', $emailList = self::getEmailListClass()::firstOrFailByUuid($request->email_list_uuid));
        $this->authorize('create', self::getSubscriberClass());

        $attributes = array_merge($request->validated(), [
            'status' => SubscriberImportStatus::Draft,
            'email_list_id' => $emailList->id,
        ]);

        unset($attributes['email_list_uuid']);

        $subscriberImport = self::getSubscriberImportClass()::create($attributes);

        return new SubscriberImportResource($subscriberImport);
    }

    public function update(SubscriberImportRequest $request, SubscriberImport $subscriberImport)
    {
        $this->authorize('update', $subscriberImport->emailList);

        if ($subscriberImport->status !== SubscriberImportStatus::Draft) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot update a non-draft import.');
        }

        $attributes = $request->validated();
        $attributes['email_list_id'] = self::getEmailListClass()::firstOrFailByUuid($attributes['email_list_uuid'])->id;
        unset($attributes['email_list_uuid']);

        $subscriberImport->update($attributes);

        return new SubscriberImportResource($subscriberImport);
    }

    public function destroy(SubscriberImport $subscriberImport)
    {
        $this->authorize('update', $subscriberImport->emailList);

        $subscriberImport->delete();

        return $this->respondOk();
    }
}
