<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Models\Subscriber;
use Symfony\Component\HttpFoundation\Response;

class ResendConfirmationMailController
{
    use RespondsToApiRequests;

    public function __invoke(
        Subscriber $subscriber,
        SendConfirmSubscriberMailAction  $sendConfirmSubscriberMailAction
    ) {
        $this->ensureUnconfirmedSubscribed($subscriber);

        $sendConfirmSubscriberMailAction->execute($subscriber);

        return $this->respondOk();
    }

    protected function ensureUnconfirmedSubscribed(Subscriber $subscriber): void
    {
        if (! $subscriber->isUnconfirmed()) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This email is not unconfirmed');
        }
    }
}