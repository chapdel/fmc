<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Tag;
use Spatie\Mailcoach\Models\TagSegment;

class DuplicateSegmentController
{
    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        /** @var \Spatie\Mailcoach\Models\TagSegment $duplicateSegment */
        $duplicateSegment = TagSegment::create([
            'name' => __('Duplicate of') . ' ' . $segment->name,
            'email_list_id' => $segment->email_list_id,
        ]);

        $positiveTagNames = $segment->positiveTags->map(fn (Tag $tag) => $tag->name)->toArray();
        $duplicateSegment->syncPositiveTags($positiveTagNames);

        $negativeTagNames = $segment->negativeTags->map(fn (Tag $tag) => $tag->name)->toArray();
        $duplicateSegment->syncNegativeTags($negativeTagNames);

        flash()->success(__('Segment :segment was duplicated.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segment.edit', [
            $duplicateSegment->emailList,
            $duplicateSegment,
        ]);
    }
}
