<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Exception;
use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscribersAction;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

class CustomImportSubscribersAction extends ImportSubscribersAction
{
    public function execute(SubscriberImport $subscriberImport, ?User $user = null): void
    {
        throw new Exception('Inside custom import action');
    }
}
