<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\Editor;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class TextEditor implements Editor
{
    public function render(HasHtmlContent $model): string
    {
        return match ($model::class) {
            Campaign::class => $this->renderForCampaign($model),
            Template::class => $this->renderForCampaignTemplate($model),
            AutomationMail::class => $this->renderForAutomationMail($model),
            TransactionalMailTemplate::class => $this->renderForTransactionalMailTemplate($model),
        };
    }

    protected function renderForCampaign(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.campaigns.partials.textEditor', [
            'html' => $model->getHtml(),
            'campaign' => $model,
        ])->render();
    }

    protected function renderForCampaignTemplate(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.campaigns.templates.partials.textEditor', [
            'html' => $model->getHtml(),
            'template' => $model,
        ])->render();
    }

    protected function renderForAutomationMail(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.automations.mails.partials.textEditor', [
            'html' => $model->getHtml(),
            'mail' => $model,
        ])->render();
    }

    protected function renderForTransactionalMailTemplate(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.transactionalMails.templates.partials.textEditor', [
            'html' => $model->getHtml(),
            'template' => $model,
        ])->render();
    }
}
