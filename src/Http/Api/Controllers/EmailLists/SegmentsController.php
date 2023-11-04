<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Queries\SegmentsQuery;
use Spatie\Mailcoach\Http\Api\Resources\SegmentResource;

class SegmentsController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function index(EmailList $emailList)
    {
        $segmentsQuery = new SegmentsQuery($emailList);

        $this->authorize('viewAny', static::getTagSegmentClass());

        $segments = $segmentsQuery->paginate();

        return SegmentResource::collection($segments);
    }

    public function show(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('view', $segment);

        $segment->load('emailList');

        return new SegmentResource($segment);
    }

    public function destroy(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('delete', $segment);

        $segment->delete();

        return $this->respondOk();
    }
}
