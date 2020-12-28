<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Http\App\Queries\CreateSegmentRequest;

class CreateSegmentController
{
    public function __invoke(EmailList $emailList, CreateSegmentRequest $request)
    {
        $segment = $emailList->segments()->create(['name' => $request->name]);

        flash()->success(__('Segment :segment has been created.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segment.edit', [$emailList, $segment]);
    }
}
