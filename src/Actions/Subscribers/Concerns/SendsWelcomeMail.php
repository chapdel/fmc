<?php


namespace Spatie\Mailcoach\Actions\Subscribers\Concerns;

use Spatie\Mailcoach\Actions\Subscribers\SendWelcomeMailAction;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;

trait SendsWelcomeMail
{
    protected function sendWelcomeMail(Subscriber $subscriber)
    {
        $sendWelcomeMailAction = Config::getActionClass('send_welcome_mail', SendWelcomeMailAction::class);

        $sendWelcomeMailAction->execute($subscriber);
    }
}
