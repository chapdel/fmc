<?php


namespace Spatie\Mailcoach\Domain\Automation\Models\Concerns;

use Illuminate\Support\Str;

abstract class AutomationStep
{
    public string $uuid;

    public function __construct(?string $uuid = null)
    {
        if (is_null($uuid)) {
            $this->uuid = Str::uuid()->toString();
        }
    }

    abstract public static function make(array $data): self;

    public function toArray(): array
    {
        return [];
    }

    public static function getName(): string
    {
        return static::class;
    }

    public function getDescription(): string
    {
        return '';
    }

    public static function getComponent(): ?string
    {
        return null;
    }
}
