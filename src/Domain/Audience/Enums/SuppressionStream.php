<?php

namespace Spatie\Mailcoach\Domain\Audience\Enums;

use Filament\Support\Contracts\HasLabel;

enum SuppressionStream: string implements HasLabel
{
    case Broadcast = 'broadcast';
    case Transactional = 'transactional';
    case Outbound = 'outbound';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Broadcast => __mc('Broadcast'),
            self::Transactional => __mc('Transactional'),
            self::Outbound => __mc('Outbound'),
        };
    }
}
