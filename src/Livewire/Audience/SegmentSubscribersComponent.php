<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class SegmentSubscribersComponent extends SubscribersComponent
{
    public EmailList $emailList;

    public TagSegment $segment;

    public function mount(EmailList $emailList, TagSegment $segment = null)
    {
        if (! $segment) {
            abort(404);
        }

        $this->emailList = $emailList;
        $this->segment = $segment;
    }

    public function getTitle(): string
    {
        return $this->segment->name;
    }

    public function getTableQuery(): Builder
    {
        return $this->segment->getSubscribersQuery();
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'segment' => $this->segment,
            'selectedSubscribersCount' => $this->segment->getSubscribersCount(),
        ];
    }
}
