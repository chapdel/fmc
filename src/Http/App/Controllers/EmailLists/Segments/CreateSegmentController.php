<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Http\App\Queries\CreateSegmentRequest;
use Spatie\Mailcoach\Models\EmailList;

class CreateSegmentController
{
    public function __invoke(EmailList $emailList, CreateSegmentRequest $request)
    {
        $segment = $emailList->segments()->create(['name' => $request->name]);

        flash()->success("Segment {$segment->name} has been created");

        return redirect()->route('mailcoach.emailLists.segment.edit', [$emailList, $segment]);
    }
}
