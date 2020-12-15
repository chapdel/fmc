<?php

namespace Spatie\Mailcoach\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\TransactionalMail;

class StoreTransactionalMail
{
    public function handle(MessageSending $sending)
    {
        ray($sending->data);

        $message = $sending->message;

        $transactionalMail = TransactionalMail::create([
            'from' => $message->getFrom(),
            'to' => $message->getTo(),
            'cc' => $message->getCc(),
            'bcc' => $message->getBcc(),
            'body' => $message->getBody(),
        ]);

        Send::create([
            'transactional_mail_id' => $transactionalMail->id,
            'sent_at' => now(),
        ]);
    }
}
