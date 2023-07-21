<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors;

use Spatie\Mailcoach\Livewire\Editor\TextAreaEditorComponent;

class TextareaEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Textarea';
    }

    public function getClass(): string
    {
        return TextAreaEditorComponent::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.textarea';
    }
}
