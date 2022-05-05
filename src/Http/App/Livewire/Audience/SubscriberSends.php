<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\SendQuery;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;

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

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.subscribers.layouts.subscriber';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'subscriber' => $this->subscriber,
            'totalSendsCount' => self::getSendClass()::query()->where('subscriber_id', $this->subscriber->id)->count(),
        ];
    }

    public function getData(): array
    {
        $this->authorize('view', $this->emailList);

        $sendQuery = new SendQuery($this->subscriber, request());

        return [
            'subscriber' => $this->subscriber,
            'sends' => $sendQuery->paginate(),
            'totalSendsCount' => self::getSendClass()::query()->where('subscriber_id', $this->subscriber->id)->count(),
        ];
    }
}
