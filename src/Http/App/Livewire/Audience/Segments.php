<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\SegmentsQuery;

class Segments extends DataTable
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function deleteSegment(int $id)
    {
        $segment = self::getTagSegmentClass()::find($id);

        $this->authorize('delete', $segment);

        $segment->delete();

        $this->flash(__('mailcoach - Segment :segment was deleted.', ['segment' => $segment->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Segments');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.segments.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
        ];
    }

    public function getData(): array
    {
        $this->authorize('view', $this->emailList);

        $segmentsQuery = new SegmentsQuery($this->emailList, request());

        return [
            'segments' => $segmentsQuery->paginate(),
            'emailList' => $this->emailList,
            'totalSegmentsCount' => $this->emailList->segments()->count(),
        ];
    }
}
