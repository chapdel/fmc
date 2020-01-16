<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\TagSegment;

class SegmentSubscribersIndexController
{
    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        $selectedSubscribersCount = $segment->getSubscribersQuery()->count();

        return view('mailcoach::app.emailLists.segment.subscribers', [
            'emailList' => $emailList,
            'segment' => $segment,
            'subscribers' => $segment->getSubscribersQuery()->paginate(),
            'selectedSubscribersCount' => $selectedSubscribersCount,
        ]);
    }
}
