<?php

namespace Spatie\Mailcoach;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\Front\Controllers\MailcoachAssets;

class Mailcoach
{
    use UsesMailcoachModels;

    public static bool $runsMigrations = true;

    protected static $editorScripts = [];
    protected static $editorStyles = [];

    public static function styles(): string
    {
        // Default to dynamic `app.css` (served by a Laravel route).
        $fullAssetPath = action([MailcoachAssets::class, 'style']);

        // Use static assets if they have been published
        if (file_exists(public_path('vendor/mailcoach/manifest.json'))) {
            $manifest = json_decode(file_get_contents(public_path('vendor/mailcoach/manifest.json')), true);
            $fullAssetPath = asset("/vendor/mailcoach/{$manifest['resources/css/app.css']['file']}");
        }

        $styles = [];

        if (is_file(__DIR__.'/../resources/hot')) {
            $url = rtrim(file_get_contents(__DIR__.'/../resources/hot'));

            $fullAssetPath = "{$url}/resources/css/app.css";
            $styles[] = sprintf('<script type="module" src="%s"></script>', "{$url}/@vite/client");
        }

        $styles[] = "<link rel=\"stylesheet\" href=\"https://pro.fontawesome.com/releases/v5.15.4/css/all.css\" integrity=\"sha384-rqn26AG5Pj86AF4SO72RK5fyefcQ/x32DNQfChxWvbXIyXFePlEktwD18fEz+kQU\" crossorigin=\"anonymous\">";
        $styles[] = "<link rel=\"stylesheet\" href=\"{$fullAssetPath}\" type=\"text/css\">";

        foreach (self::availableEditorStyles() as $editor => $editorStyles) {
            if (! in_array($editor, [config('mailcoach.content_editor'), config('mailcoach.template_editor')])) {
                continue;
            }

            foreach ($editorStyles as $style) {
                $styles[] = "<link rel=\"stylesheet\" href=\"{$style}\">";
            }
        }

        return implode("\n", $styles);
    }

    public static function scripts(): string
    {
        // Default to dynamic `app.js` (served by a Laravel route).
        $fullAssetPath = action([MailcoachAssets::class, 'script']);

        // Use static assets if they have been published
        if (file_exists(public_path('vendor/mailcoach/manifest.json'))) {
            $manifest = json_decode(file_get_contents(public_path('vendor/mailcoach/manifest.json')), true);
            $fullAssetPath = asset("/vendor/mailcoach/{$manifest['resources/js/app.js']['file']}");
        }

        $scripts = [];

        foreach (self::availableEditorScripts() as $editor => $editorScripts) {
            if (! in_array($editor, [config('mailcoach.content_editor'), config('mailcoach.template_editor')])) {
                continue;
            }

            foreach ($editorScripts as $script) {
                $scripts[] = "<script type=\"text/javascript\" src=\"{$script}\"></script>";
            }
        }

        if (is_file(__DIR__.'/../resources/hot')) {
            $url = rtrim(file_get_contents(__DIR__.'/../resources/hot'));

            $scripts[] = sprintf('<script type="module" src="%s"></script>', "{$url}/resources/js/app.js");
            $scripts[] = sprintf('<script type="module" src="%s"></script>', "{$url}/@vite/client");
        } else {
            $scripts[] = <<<HTML
                <script src="{$fullAssetPath}" data-turbo-eval="false" data-turbolinks-eval="false"></script>
            HTML;
        }

        return implode("\n", $scripts);
    }

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
