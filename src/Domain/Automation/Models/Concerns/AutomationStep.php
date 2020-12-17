<?php


namespace Spatie\Mailcoach\Domain\Automation\Models\Concerns;

abstract class AutomationStep
{
    public ?string $uuid = null;

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
