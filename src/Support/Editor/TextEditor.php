<?php

namespace Spatie\Mailcoach\Support\Editor;

use Spatie\Mailcoach\Models\Concerns\HasHtmlContent;

class TextEditor implements Editor
{
    public function render(HasHtmlContent $model): string
    {
        return view('mailcoach::app.campaigns.draft.textEditor', [
            'html' => $model->getHtml(),
        ])->render();
    }
}
