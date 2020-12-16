<?php

namespace Spatie\Mailcoach\Support\Editor;

use Spatie\Mailcoach\Models\Concerns\HasHtmlContent;

class TextEditor implements Editor
{
    public function render(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.campaigns.partials.textEditor', [
            'html' => $model->getHtml(),
            'campaign' => $model,
        ])->render();
    }
}
