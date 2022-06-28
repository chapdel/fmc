<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;

class Subscribers extends DataTable
{
    public string $sort = '-created_at';

    protected array $allowedFilters = [
        'status' => ['except' => ''],
    ];

    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists.subscribers', $this->emailList));
    }

    public function deleteSubscriber(int $id)
    {
        $subscriber = self::getSubscriberClass()::find($id);

        $this->authorize('delete', $subscriber);

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Mailcoach::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($subscriber);

        $this->flash(__('mailcoach - Subscriber :subscriber was deleted.', ['subscriber' => $subscriber->email]));
    }

    public function resubscribe(int $id)
    {
        $subscriber = self::getSubscriberClass()::find($id);

        if (! $subscriber->isUnsubscribed()) {
            $this->flash(__('mailcoach - Can only resubscribe unsubscribed subscribers'), 'error');

            return;
        }

        $subscriber->update([
            'unsubscribed_at' => null,
        ]);

        $this->flash(__('mailcoach - :subscriber has been resubscribed.', ['subscriber' => $subscriber->email]));
    }

    public function unsubscribe(int $id)
    {
        $subscriber = self::getSubscriberClass()::find($id);

        if (! $subscriber->isSubscribed()) {
            $this->flash(__('mailcoach - Can only unsubscribe a subscribed subscriber'), 'error');

            return;
        }

        $subscriber->unsubscribe();

        $this->flash(__('mailcoach - :subscriber has been unsubscribed.', ['subscriber' => $subscriber->email]));
    }

    public function confirm(int $id)
    {
        $subscriber = self::getSubscriberClass()::find($id);

        if ($subscriber->status !== SubscriptionStatus::Unconfirmed) {
            $this->flash(__('mailcoach - Can only subscribe unconfirmed emails'), 'error');

            return;
        }

        $subscriber->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $this->flash(__('mailcoach - :subscriber has been confirmed.', ['subscriber' => $subscriber->email]));
    }

    public function resendConfirmation(int $id)
    {
        $subscriber = self::getSubscriberClass()::find($id);

        resolve(SendConfirmSubscriberMailAction::class)->execute($subscriber);

        $this->flash(__('mailcoach - A confirmation mail has been sent to :subscriber', ['subscriber' => $subscriber->email]));
    }

    public function deleteUnsubscribes()
    {
        $this->authorize('update', $this->emailList);

        $this->emailList->allSubscribersWithoutIndex()->unsubscribed()->delete();

        $this->flash(__('mailcoach - All unsubscribers of the list have been deleted.'));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Subscribers');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.subscribers.index';
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

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        $subscribersQuery = new EmailListSubscribersQuery($this->emailList, $request);

        return [
            'subscribers' => $subscribersQuery->paginate(),
            'emailList' => $this->emailList,
            'allSubscriptionsCount' => $this->emailList->allSubscribers()->count(),
            'totalSubscriptionsCount' => $this->emailList->subscribers()->count(),
            'unconfirmedCount' => $this->emailList->allSubscribers()->unconfirmed()->count(),
            'unsubscribedCount' => $this->emailList->allSubscribers()->unsubscribed()->count(),
        ];
    }
}
