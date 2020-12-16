<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments;

use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\TagSegment;

class DestroySegmentController
{
    public function __invoke(EmailList $emailList, TagSegment $segment)
    {
        $segment->delete();

        flash()->success(__('Segment :segment was deleted.', ['segment' => $segment->name]));

        return back();
    }
}
