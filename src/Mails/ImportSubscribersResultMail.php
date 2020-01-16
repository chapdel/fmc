<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Models\SubscriberImport;

class ImportSubscribersResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $theme = 'mailcoach::mails.layout.mailcoach';
    
    public SubscriberImport $subscriberImport;

    public function __construct(SubscriberImport $subscriberImport)
    {
        $this->subscriberImport = $subscriberImport;
    }

    public function build()
    {
        return $this->markdown('mailcoach::mails.importResults');
    }
}
