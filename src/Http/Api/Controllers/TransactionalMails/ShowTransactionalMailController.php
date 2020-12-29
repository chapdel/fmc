<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Spatie\Mailcoach\Http\Api\Resources\TransactionalMailResource;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class ShowTransactionalMailController
{
    public function __invoke(TransactionalMail $transactionalMail)
    {
        return new TransactionalMailResource($transactionalMail);
    }
}
