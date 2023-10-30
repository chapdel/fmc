<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Editor\Textarea\TextareaEditorConfigurationDriver;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class EditorConfigurationDriverRepository
{
    /** @return Collection<EditorConfigurationDriver> */
    public function getSupportedEditors(): Collection
    {
        return collect(config('mailcoach.editors'))
            /** @var class-string<EditorConfigurationDriver> $editorConfigurationDriver */
            ->map(function (string $editorConfigurationDriver) {
                return resolve($editorConfigurationDriver);
            });
    }

    public function getForEditor(string $editorLabel): EditorConfigurationDriver
    {
        $configuredEditor = $this->getSupportedEditors()
            ->first(fn (EditorConfigurationDriver $editor) => $editorLabel === $editor->label());

        return $configuredEditor ?? app(TextareaEditorConfigurationDriver::class);
    }

    public function getForClass(string $class): EditorConfigurationDriver
    {
        $configuredEditor = $this->getSupportedEditors()
            ->first(fn (EditorConfigurationDriver $editor) => $class === $editor->getClass());

        return $configuredEditor ?? app(TextareaEditorConfigurationDriver::class);
    }
}
