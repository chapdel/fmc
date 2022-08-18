<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors;

use Spatie\MailcoachUnlayer\UnlayerEditor;

class UnlayerEditorConfigurationDriver extends EditorConfigurationDriver
{
    public static function label(): string
    {
        return 'Unlayer';
    }

    public function getClass(): string
    {
        return UnlayerEditor::class;
    }

    public static function settingsPartial(): ?string
    {
        return 'mailcoach::app.configuration.editor.partials.unlayer';
    }
}
