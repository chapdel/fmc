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

        $sendAt = now()->addMinutes($subscriber->emailList->welcome_mail_delay_in_minutes);

        Mail::mailer($subscriber->emailList->transactional_mailer)
            ->to($subscriber->email)
            ->later(
                $sendAt,
                $this->getMailable($subscriber),
                config('mailcoach.perform_on_queue.send_welcome_mail_job', 'mailcoach')
            );
    }

    protected function getMailable(Subscriber $subscriber): Mailable
    {
        $mailableClass = $subscriber->emailList->welcomeMailableClass();

        return app()->makeWith($mailableClass, ['subscriber' => $subscriber]);
    }
}
