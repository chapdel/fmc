<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Exception;
use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\ImportSubscribersAction;
use Spatie\Mailcoach\Domain\Campaign\Models\SubscriberImport;

class CustomImportSubscribersAction extends ImportSubscribersAction
{
    public function execute(SubscriberImport $subscriberImport, ?User $user = null): void
    {
        throw new Exception('Inside custom import action');
    }
}
