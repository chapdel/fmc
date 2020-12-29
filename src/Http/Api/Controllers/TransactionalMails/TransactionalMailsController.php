<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Spatie\Mailcoach\Http\Api\Resources\TransactionalMailResource;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailsQuery;

class TransactionalMailsController
{
    public function __invoke(TransactionalMailsQuery $transactionalMailsQuery)
    {
        return TransactionalMailResource::collection($transactionalMailsQuery->paginate());
    }
}
