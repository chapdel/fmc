<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Subscribers\UpdateSubscriberRequest;
use Spatie\Mailcoach\Http\App\ViewModels\SubscriberViewModel;

class SubscriberDetailsController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList, Subscriber $subscriber)
    {
        $this->authorize('view', $emailList);

        return view('mailcoach::app.emailLists.subscribers.edit', new SubscriberViewModel($subscriber));
    }

    public function update(
        EmailList $emailList,
        Subscriber $subscriber,
        UpdateSubscriberRequest $request
    ) {
        $this->authorize('update', $emailList);

        $updateSubscriberAction = Config::getCampaignActionClass('update_subscriber', UpdateSubscriberAction::class);

        $updateSubscriberAction->execute(
            $subscriber,
            $request->subscriberAttributes(),
            $request->tags ?? [],
        );

        flash()->success(__('Subscriber :subscriber was updated.', ['subscriber' => $subscriber->email]));

        return redirect()->route('mailcoach.emailLists.subscriber.details', [$subscriber->emailList, $subscriber]);
    }
}
