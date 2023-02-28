<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\TagSegmentRequest;
use Spatie\Mailcoach\Http\Api\Resources\SegmentResource;
use Spatie\Mailcoach\Http\App\Queries\SegmentsQuery;

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

    public function store(EmailList $emailList, TagSegmentRequest $request)
    {
        $tagClass = self::getTagSegmentClass();

        $this->authorize('create', $tagClass);

        $segment = $emailList->segments()->create([
            'name' => $request->validated('name'),
            'all_positive_tags_required' => $request->validated('all_positive_tags_required', false),
            'all_negative_tags_required' => $request->validated('all_negative_tags_required', false),
        ]);

        $segment
            ->syncPositiveTags($request->validated('positive_tags', []))
            ->syncNegativeTags($request->validated('negative', []));

        return SegmentResource::make($segment);
    }

    public function update(EmailList $emailList, TagSegment $segment, TagSegmentRequest $request)
    {
        $this->authorize('update', $segment);

        $segment->update([
            'name' => $request->validated('name'),
            'all_positive_tags_required' => $request->validated('all_positive_tags_required', false),
            'all_negative_tags_required' => $request->validated('all_negative_tags_required', false),
        ]);

        $segment
            ->syncPositiveTags($request->validated('positive_tags', []))
            ->syncNegativeTags($request->validated('negative', []));

        return SegmentResource::make($segment);
    }

    public function destroy(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('delete', $segment);

        $segment->delete();

        return $this->respondOk();
    }
}
