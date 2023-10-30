<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Actions\SendTransactionalMailAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\SuppressedEmail;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTransactionalMailRequest;

class SendTransactionalMailController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;
    use UsesMailcoachModels;

    public function __invoke(SendTransactionalMailRequest $request, SendTransactionalMailAction $sendTransactionalMailAction)
    {
        $this->authorize(
            'send',
            [static::getSendClass(), $request->getFromEmail(), $request->getToEmails()],
        );

        try {
            $sendTransactionalMailAction->execute($request);
        } catch (SuppressedEmail|\InvalidArgumentException $exception) {
            return $this->respondNotAcceptable($exception->getMessage());
        }

        return $this->respondOk();
    }
}
