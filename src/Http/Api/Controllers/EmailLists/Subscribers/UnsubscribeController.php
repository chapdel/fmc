<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Models\Subscriber;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeController
{
    use RespondsToApiRequests;

    public function __invoke(Subscriber $subscriber)
    {
        $this->ensureSubscribedSubscriber($subscriber);

        $subscriber->unsubscribe();

        return $this->respondOk();
    }

    protected function ensureSubscribedSubscriber(Subscriber $subscriber): void
    {
        if (! $subscriber->isSubscribed()) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This email was not subscribed');
        }
    }
}