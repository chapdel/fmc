<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Editor;

use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;

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
