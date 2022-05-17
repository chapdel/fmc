<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\SendQuery;

class SubscriberSends extends DataTable
{
    public string $sort = '-sent_at';

    public EmailList $emailList;

    public Subscriber $subscriber;

    public function mount(EmailList $emailList, Subscriber $subscriber)
    {
        $this->emailList = $emailList;
        $this->subscriber = $subscriber;
    }

    public function getTitle(): string
    {
        return $this->subscriber->email;
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.subscribers.sends';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'subscriber' => $this->subscriber,
            'totalSendsCount' => self::getSendClass()::query()->where('subscriber_id', $this->subscriber->id)->count(),
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        $sendQuery = new SendQuery($this->subscriber, $request);

        return [
            'subscriber' => $this->subscriber,
            'sends' => $sendQuery->paginate(),
            'totalSendsCount' => self::getSendClass()::query()->where('subscriber_id', $this->subscriber->id)->count(),
        ];
    }
}
