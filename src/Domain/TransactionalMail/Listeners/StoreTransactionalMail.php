<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;

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
            'subject' => $message->getSubject(),
            'from' => $this->convertToNamedArray($message->getFrom()),
            'to' => $this->convertToNamedArray($message->getTo()),
            'cc' => $this->convertToNamedArray($message->getCc()),
            'bcc' => $this->convertToNamedArray($message->getBcc()),
            'body' => $message->getBody(),
            'track_opens' => $messageConfig->trackOpens(),
            'track_clicks' => $messageConfig->trackClicks(),
            'mailable_class' => $messageConfig->getMailableClass(),
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
            ->map(fn (?string $name, string $email) => compact('email', 'name'))
            ->values()
            ->toArray();
    }
}
