<?php

namespace Spatie\Mailcoach\Support;

use Swift_Message;

class TransactionalMailMessageConfig
{
    public const HEADER_NAME_OPENS = 'mailcoach-transactional-mail-config-track-opens';
    public const HEADER_NAME_CLICKS = 'mailcoach-transactional-mail-config-track-clicks';
    public const HEADER_NAME_STORE = 'mailcoach-transactional-mail-config-store';
    public const HEADER_NAME_MAILABLE_CLASS = 'mailcoach-transactional-mail-config-mailable-class';

    protected Swift_Message $message;

    public static function createForMessage(Swift_Message $message): self
    {
        return new self($message);
    }

    protected function __construct(Swift_Message $message)
    {
        $this->message = $message;
    }

    public function trackOpens(): bool
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_OPENS);
    }

    public function trackClicks(): bool
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_CLICKS);
    }

    public function shouldStore(): bool
    {
        if ($this->trackOpens()) {
            return true;
        }

        if ($this->trackClicks()) {
            return true;
        }

        return $this->message->getHeaders()->has(static::HEADER_NAME_STORE);
    }

    public function getMailableClass(): string
    {
        return $this->message->getHeaders()->has(static::HEADER_NAME_MAILABLE_CLASS);
    }

    public static function getHeaderNames(): array
    {
        return [
            static::HEADER_NAME_OPENS,
            static::HEADER_NAME_CLICKS,
            static::HEADER_NAME_STORE,
            static::HEADER_NAME_MAILABLE_CLASS,
        ];
    }
}
