<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Http\App\Requests\UpdateSegmentRequest;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\TagSegment;

class EditSegmentController
{
    public function edit(EmailList $emailList, TagSegment $segment)
    {
        $selectedSubscribersCount = $segment->getSubscribersQuery()->count();

        return view('mailcoach::app.emailLists.segment.edit', [
            'emailList' => $emailList,
            'segment' => $segment,
            'selectedSubscribersCount' => $selectedSubscribersCount,
        ]);
    }

    public function update(EmailList $emailList, TagSegment $segment, UpdateSegmentRequest $request)
    {
        $segment->update([
            'name' => $request->name,
            'all_positive_tags_required' => $request->allPositiveTagsRequired(),
            'all_negative_tags_required' => $request->allNegativeTagsRequired(),
        ]);

        $segment
            ->syncPositiveTags($request->positive_tags ?? [])
            ->syncNegativeTags($request->negative_tags ?? []);

        flash()->success(__('The segment has been updated.'));

        return redirect()->route('mailcoach.emailLists.segment.subscribers', [$emailList, $segment]);
    }
}
