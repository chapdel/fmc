<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\ConfirmSubscriberRequest;
use Spatie\Mailcoach\Models\Subscriber;
use Symfony\Component\HttpFoundation\Response;

class ConfirmSubscriberController
{
    use RespondsToApiRequests;

    public function __invoke(
        ConfirmSubscriberRequest $request,
        Subscriber $subscriber,
        ConfirmSubscriberAction $confirmSubscriberAction
    ) {
        $this->ensureUnconfirmedSubscriber($subscriber);

        $confirmSubscriberAction->doNotSendWelcomeMail()->execute($subscriber);

        $this->respondOk();
    }

    protected function ensureUnconfirmedSubscriber(Subscriber $subscriber): void
    {
        if ($subscriber->status !== SubscriptionStatus::UNCONFIRMED) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'The subscriber was already confirmed');
        }
    }
}
