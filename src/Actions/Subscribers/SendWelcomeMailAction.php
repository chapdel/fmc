<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Models\Subscriber;

class SendWelcomeMailAction
{
    public function execute(Subscriber $subscriber)
    {
        if (! $subscriber->emailList->send_welcome_mail) {
            return;
        }

        Mail::mailer($subscriber->emailList->transactional_mailer)
            ->to($subscriber->email)
            ->queue($this->getMailable($subscriber));
    }

    protected function getMailable(Subscriber $subscriber): Mailable
    {
        $mailableClass = $subscriber->emailList->welcomeMailableClass();

        return app()->makeWith($mailableClass, ['subscriber' => $subscriber]);
    }
}
