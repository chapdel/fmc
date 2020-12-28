<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\SegmentsQuery;

class SegmentsIndexController
{
    public function __invoke(EmailList $emailList)
    {
        $segmentsQuery = new SegmentsQuery($emailList);

        return view('mailcoach::app.emailLists.segments', [
            'segments' => $segmentsQuery->paginate(),
            'emailList' => $emailList,
            'totalSegmentsCount' => $emailList->segments()->count(),
        ]);
    }
}
