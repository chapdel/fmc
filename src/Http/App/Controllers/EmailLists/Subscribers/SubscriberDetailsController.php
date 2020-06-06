<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Http\App\Requests\UpdateSubscriberRequest;
use Spatie\Mailcoach\Http\App\ViewModels\SubscriberViewModel;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;

class SubscriberDetailsController
{
    public function edit(EmailList $emailList, Subscriber $subscriber)
    {
        return view('mailcoach::app.emailLists.subscriber.edit', new SubscriberViewModel($subscriber));
    }

    public function update(
        EmailList $emailList,
        Subscriber $subscriber,
        UpdateSubscriberRequest $request
    ) {
        $updateSubscriberAction = Config::getActionClass('update_subscriber', UpdateSubscriberAction::class);

        $updateSubscriberAction->execute(
            $subscriber,
            $request->subscriberAttributes(),
            $request->tags ?? [],
        );

        flash()->success(__('Subscriber :subscriber was updated.', ['subscriber' => $subscriber->email]));

        return redirect()->route('mailcoach.emailLists.subscriber.details', [$subscriber->emailList, $subscriber]);
    }
}
