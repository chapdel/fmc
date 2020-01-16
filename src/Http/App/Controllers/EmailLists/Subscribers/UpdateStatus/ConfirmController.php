<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\Subscriber;
use Symfony\Component\HttpFoundation\Response;

class ConfirmController
{
    public function __invoke(Subscriber $subscriber)
    {
        $this->ensureUnconfirmedSubscriber($subscriber);

        $subscriber->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        flash()->success("{$subscriber->email} has been confirmed.");

        return back();
    }

    protected function ensureUnconfirmedSubscriber(Subscriber $subscriber): void
    {
        if ($subscriber->status !== SubscriptionStatus::UNCONFIRMED) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'can only subscribe unconfirmed emails');
        }
    }
}
