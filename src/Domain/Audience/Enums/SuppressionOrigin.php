<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

use Filament\Support\Contracts\HasLabel;

enum SuppressionOrigin: string implements HasLabel
{
    case Admin = 'admin';
    case Client = 'client';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => __mc('Admin'),
            self::Client => __mc('Client'),
        };
    }
}
