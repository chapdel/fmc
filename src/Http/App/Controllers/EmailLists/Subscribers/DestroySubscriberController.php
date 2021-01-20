<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class DestroySubscriberController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, Subscriber $subscriber)
    {
        $this->authorize('update', $emailList);

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Config::getCampaignActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($subscriber);

        flash()->success(__('Subscriber :subscriber was deleted.', ['subscriber' => $subscriber->email]));

        return back();
    }
}
