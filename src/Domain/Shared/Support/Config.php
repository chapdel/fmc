<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;

class Config
{
    public static function getActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.actions.{$actionName}");

        if (is_null($configuredClass)) {
            $configuredClass = $actionClass;
        }

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfig::invalidAction($actionName, $configuredClass ?? '', $actionClass);
        }

        return app($configuredClass);
    }

    public static function getQueueConnection(): ?string
    {
        return config('mailcoach.queue_connection') ?? env('QUEUE_CONNECTION');
    }
}
