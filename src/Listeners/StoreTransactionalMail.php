<?php

namespace Spatie\Mailcoach\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\TransactionalMail;
use Spatie\Mailcoach\Support\TransactionalMailMessageConfig;

class StoreTransactionalMail
{
    public function handle(MessageSending $sending): void
    {
        $message = $sending->message;

        $messageConfig = TransactionalMailMessageConfig::createForMessage($message);

        if (! $messageConfig->shouldStore()) {
            return;
        }

        $transactionalMail = TransactionalMail::create([
            'from' => $message->getFrom(),
            'to' => $message->getTo(),
            'cc' => $message->getCc(),
            'bcc' => $message->getBcc(),
            'body' => $message->getBody(),
            'track_opens' => $messageConfig->trackOpens(),
            'track_clicks' => $messageConfig->trackClicks(),
        ]);

        Send::create([
            'transactional_mail_id' => $transactionalMail->id,
            'sent_at' => now(),
        ]);
    }
}
