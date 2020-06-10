<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Models\Subscriber;

class UnsubscribeController
{
    public function __invoke(Subscriber $subscriber)
    {
        if (! $subscriber->isSubscribed()) {
            flash()->error(__('Can only unsubscribe a subscribed subscriber'));

            return back();
        }

        $subscriber->update([
            'unsubscribed_at' => now(),
        ]);

        flash()->success(__(':subscriber has been unsubscribed.', ['subscriber' => $subscriber->email]));

        return back();
    }
}
