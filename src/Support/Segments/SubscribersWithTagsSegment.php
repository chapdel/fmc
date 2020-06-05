<?php

namespace Spatie\Mailcoach\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Models\TagSegment as TagSegmentModel;

class SubscribersWithTagsSegment extends Segment
{
    public function description(): string
    {
        if ($this->getTagSegmentModel()) {
            return $this->getTagSegmentModel()->name;
        }

        return __('deleted segment');
    }

    public function subscribersQuery(Builder $subscribersQuery): void
    {
        $tagSegment = $this->getTagSegmentModel();

        if (! $tagSegment) {
            return;
        }

        $tagSegment->scopeOnTags($subscribersQuery);
    }

    public function getTagSegmentModel(): ?TagSegmentModel
    {
        return TagSegmentModel::find($this->campaign->segment_id);
    }
}
