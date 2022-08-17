<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Validation\Rule;
use Spatie\MailcoachMarkdownEditor\Editor;

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

    public function validationRules(): array
    {
        return [];
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.markdown';
    }
}
