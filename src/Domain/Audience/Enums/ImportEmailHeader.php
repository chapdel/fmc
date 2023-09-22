<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

enum ImportEmailHeader: string
{
    case email = 'email';
    case Email = 'Email'; // Substack
    case Email_Address = 'Email Address'; // Mailchimp

    public static function values(): array
    {
        return [
            self::email->value,
            self::Email->value,
            self::Email_Address->value,
        ];
    }
}
