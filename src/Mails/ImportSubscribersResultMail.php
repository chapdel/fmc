<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Models\SubscriberImport;

class ImportSubscribersResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public SubscriberImport $subscriberImport;

    public function __construct(SubscriberImport $subscriberImport)
    {
        $this->subscriberImport = $subscriberImport;
    }

    public function build()
    {
        return $this
            ->from(
                $this->subscriberImport->emailList->default_from_email,
                $this->subscriberImport->emailList->default_from_name
            )
            ->subject(__('Import Subscribers Result Mail'))
            ->markdown('mailcoach::mails.importResults');
    }
}
