<?php


namespace Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\Concerns;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\SendWelcomeMailAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

trait SendsWelcomeMail
{
    protected function sendWelcomeMail(Subscriber $subscriber): void
    {
        $sendWelcomeMailAction = Config::getCampaignActionClass('send_welcome_mail', SendWelcomeMailAction::class);

        $sendWelcomeMailAction->execute($subscriber);
    }
}
