<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Exception;
use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Actions\Subscribers\ImportSubscribersAction;
use Spatie\Mailcoach\Models\SubscriberImport;

class CustomImportSubscribersAction extends ImportSubscribersAction
{
    public function execute(SubscriberImport $subscriberImport, ?User $user = null)
    {
        throw new Exception('Inside custom import action');
    }
}
