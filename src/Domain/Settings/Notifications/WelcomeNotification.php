<?php

namespace Spatie\Mailcoach\Domain\Settings\Notifications;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\URL;

class WelcomeNotification extends \Spatie\WelcomeNotification\WelcomeNotification
{
    protected function initializeNotificationProperties(User $user): void
    {
        $this->user = $user;

        $this->user->welcome_valid_until = $this->validUntil;
        $this->user->save();

        $this->showWelcomeFormUrl = URL::temporarySignedRoute(
            'welcome',
            $this->validUntil,
            ['mailcoachUser' => $user->welcomeNotificationKeyValue()] // @todo method does not exist?
        );
    }
}
