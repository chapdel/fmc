<?php

namespace Spatie\Mailcoach\Domain\Settings\Enums;

enum MailerTransport: string
{
    case Ses = 'ses';
    case SendGrid = 'sendGrid';
    case Smtp = 'smtp';
    case Postmark = 'postmark';
    case Mailgun = 'mailgun';

    public function label(): string
    {
        return match ($this) {
            self::Ses => 'Amazon SES',
            self::SendGrid => 'SendGrid',
            self::Smtp => 'SMTP',
            self::Postmark => 'Postmark',
            self::Mailgun => 'Mailgun',
        };
    }
}
