<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\TagSegment;

class SegmentSubscribersIndexController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        $this->authorize('view', $emailList);

        $selectedSubscribersCount = $segment->getSubscribersQuery()->count();

        return view('mailcoach::app.emailLists.segment.subscribers', [
            'emailList' => $emailList,
            'segment' => $segment,
            'subscribers' => $segment->getSubscribersQuery()->paginate(),
            'selectedSubscribersCount' => $selectedSubscribersCount,
        ]);
    }
}
