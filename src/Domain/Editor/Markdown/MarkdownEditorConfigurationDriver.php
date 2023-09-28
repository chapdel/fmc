<?php

namespace Spatie\Mailcoach\Domain\Editor\Markdown;

use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors\EditorConfigurationDriver;

class MarkdownEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Markdown';
    }

    public function getClass(): string
    {
        return Editor::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.markdown';
    }
}
