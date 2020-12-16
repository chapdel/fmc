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
            'from' => $this->convertToNamedArray($message->getFrom()),
            'to' => $this->convertToNamedArray($message->getTo()),
            'cc' => $this->convertToNamedArray($message->getCc()),
            'bcc' => $this->convertToNamedArray($message->getBcc()),
            'body' => $message->getBody(),
            'track_opens' => $messageConfig->trackOpens(),
            'track_clicks' => $messageConfig->trackClicks(),
        ]);

        $send = Send::create([
            'transactional_mail_id' => $transactionalMail->id,
            'sent_at' => now(),
        ]);


        $send->storeTransportMessageId($message->getId());
    }

    public function convertToNamedArray(?array $persons): array
    {
        return collect($persons ?? [])
            ->map(fn(?string $name, string $email) => compact('email', 'name'))
            ->values()
            ->toArray();
    }
}
