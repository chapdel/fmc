<?php

namespace Spatie\Mailcoach;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Mailcoach
{
    use UsesMailcoachModels;

    public static bool $runsMigrations = true;

    protected static $editorScripts = [];
    protected static $editorStyles = [];

    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    public static function availableEditorScripts()
    {
        return static::$editorScripts;
    }

    public static function editorScript(string $editor, string $url)
    {
        static::$editorScripts[$editor][] = $url;

        return new static;
    }

    public static function availableEditorStyles()
    {
        return static::$editorStyles;
    }

    public static function editorStyle(string $editor, string $url)
    {
        static::$editorStyles[$editor][] = $url;

        return new static;
    }

    public static function getCampaignActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.campaigns.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getSharedActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.shared.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getAutomationActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.automation.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getAudienceActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.audience.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    public static function getTransactionalActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("mailcoach.transactional.actions.{$actionName}");

        return self::getActionClass($configuredClass, $actionName, $actionClass);
    }

    protected static function getActionClass(?string $configuredClass, string $actionName, string $actionClass): object
    {
        if (is_null($configuredClass)) {
            $configuredClass = $actionClass;
        }

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfig::invalidAction($actionName, $configuredClass, $actionClass);
        }

        return resolve($configuredClass);
    }

    public static function getLivewireClass(string $componentName, string $defaultClass): string
    {
        $configuredClass = config("mailcoach.livewire.components.{$componentName}", $defaultClass);

        if (! is_subclass_of($configuredClass, Component::class)) {
            throw InvalidConfig::invalidLivewireComponent($componentName, $configuredClass);
        }

        return $configuredClass;
    }

    public static function getQueueConnection(): ?string
    {
        return config('mailcoach.queue_connection') ?? env('QUEUE_CONNECTION');
    }
}
