<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums;

enum ActionCategoryEnum: string
{
    case Tags = 'tags';
    case Check = 'check';
    case Pause = 'pause';
    case React = 'react';

    public function label(): string
    {
        return match($this) {
            self::Tags => __('Tags'),
            self::Check => __('Check'),
            self::Pause => __('Pause'),
            self::React => __('React'),
        };
    }

    protected static function labels(): array
    {
        return [
            'tags' => 'Tags',
            'check' => 'Route',
            'pause' => 'Pause',
            'react' => 'Respond',
        ];
    }

    public static function icons(): array
    {
        return [
            'tags' => 'fa-tag',
            'check' => 'fa-random',
            'pause' => 'fa-clock',
            'react' => 'fa-cogs',
        ];
    }
}
