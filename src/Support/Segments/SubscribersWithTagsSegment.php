<?php

namespace Spatie\Mailcoach\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Models\TagSegment as TagSegmentModel;

class SubscribersWithTagsSegment extends Segment
{
    public function description(): string
    {
        return $this->getTagSegmentModel()->name;
    }

    public function subscribersQuery(Builder $subscribersQuery): void
    {
        $tagSegment = $this->getTagSegmentModel();

        $tagSegment->scopeOnTags($subscribersQuery);
    }

    public function getTagSegmentModel(): TagSegmentModel
    {
        return TagSegmentModel::find($this->campaign->segment_id);
    }
}
