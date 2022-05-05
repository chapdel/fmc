<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;

class SegmentSubscribers extends DataTable
{
    public string $sort = 'email';

    public EmailList $emailList;

    public TagSegment $segment;

    public function mount(EmailList $emailList, TagSegment $segment)
    {
        $this->emailList = $emailList;
        $this->segment = $segment;
    }

    public function getTitle(): string
    {
        return $this->segment->name;
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.segments.subscribers';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.segments.layouts.segment';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'segment' => $this->segment,
            'selectedSubscribersCount' => $this->segment->getSubscribersQuery()->count(),
        ];
    }

    public function getData(): array
    {
        $this->authorize('view', $this->emailList);

        return [
            'emailList' => $this->emailList,
            'segment' => $this->segment,
            'subscribers' => $this->segment->getSubscribersQuery()->paginate(),
            'subscribersCount' => $this->emailList->subscribers()->count(),
            'selectedSubscribersCount' => $this->segment->getSubscribersQuery()->count(),
        ];
    }
}
