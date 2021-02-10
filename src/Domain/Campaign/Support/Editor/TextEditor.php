<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Editor;

use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class TextEditor implements Editor
{
    public function render(HasHtmlContent $model): string
    {
        return $model instanceof TransactionalMailTemplate
            ? $this->renderForTransactionalMailTemplate($model)
            : $this->renderForCampaign($model);
    }

    protected function renderForTransactionalMailTemplate(TransactionalMailTemplate $model): string
    {
        return (string)view('mailcoach::app.transactionalMails.templates.partials.textEditor', [
            'html' => $model->getHtml(),
            'template' => $model,
        ])->render();
    }

    protected function renderForCampaign(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.campaigns.partials.textEditor', [
            'html' => $model->getHtml(),
            'campaign' => $model,
        ])->render();
    }
}
