<?php


namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\Concerns;

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendWelcomeMailAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Mailcoach;

trait SendsWelcomeMail
{
    protected function sendWelcomeMail(Subscriber $subscriber): void
    {
        $sendWelcomeMailAction = Mailcoach::getAudienceActionClass('send_welcome_mail', SendWelcomeMailAction::class);

        $sendWelcomeMailAction->execute($subscriber);
    }
}
