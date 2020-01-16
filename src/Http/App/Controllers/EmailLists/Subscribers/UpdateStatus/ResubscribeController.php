<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Models\Subscriber;

class ResubscribeController
{
    public function __invoke(Subscriber $subscriber)
    {
        if (! $subscriber->isUnsubscribed()) {
            flash()->error('Can only resubscribe unsubscribed subscribers');

            return back();
        }

        $subscriber->update([
            'unsubscribed_at' => null,
        ]);

        flash()->success("{$subscriber->email} has been resubscribed.");

        return back();
    }
}
