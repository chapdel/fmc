<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Http\App\Queries\SegmentsQuery;
use Spatie\Mailcoach\Models\EmailList;

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
