<?php

namespace Spatie\Mailcoach\Livewire\Templates;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template as TemplateModel;
use Spatie\Mailcoach\Domain\Template\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TemplateComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public TemplateModel $template;

    #[Rule('required')]
    public ?string $name;

    #[Rule('required')]
    public ?string $html;

    public function mount(TemplateModel $template)
    {
        $this->authorize('update', $template);

        $this->template = $template;
        $this->name = $template->name;
        $this->html = $template->getHtml();
    }

    public function save()
    {
        $this->validate();

        $this->dispatch('saveContent');
    }

    #[On('editorSaved')]
    public function updateTemplate()
    {
        $this->template->refresh();

        $this->template->name = $this->name;
        $this->template->save();

        $this->reRenderEmailsUsingTemplate();

        notify(__mc('Template :template was updated.', ['template' => $this->template->name]));
    }

    #[On('editorUpdated')]
    public function updatePreviewHtml($uuid, $previewHtml)
    {
        $this->html = $previewHtml;
    }

    private function reRenderEmailsUsingTemplate(): void
    {
        $templateRenderer = (new TemplateRenderer($this->template->html ?? ''));

        self::getCampaignClass()::query()
            ->where('status', CampaignStatus::Draft)
            ->whereHas('contentItem', fn (Builder $query) => $query->where('template_id', $this->template->id))
            ->each(function (Campaign $campaign) use ($templateRenderer) {
                $campaign->contentItems->where('template_id', $this->template->id)->each(function (ContentItem $contentItem) use ($templateRenderer) {
                    $contentItem->setHtml($templateRenderer->render($contentItem->getTemplateFieldValues()));
                    $contentItem->save();
                });
            });

        self::getTransactionalMailClass()::query()
            ->whereHas('contentItem', fn (Builder $query) => $query->where('template_id', $this->template->id))
            ->each(function (TransactionalMail $mail) use ($templateRenderer) {
                $mail->contentItem->setHtml($templateRenderer->render($mail->contentItem->getTemplateFieldValues()));
                $mail->contentItem->save();
            });

        self::getAutomationMailClass()::query()
            ->whereHas('contentItem', fn (Builder $query) => $query->where('template_id', $this->template->id))
            ->each(function (AutomationMail $mail) use ($templateRenderer) {
                $mail->contentItem->setHtml($templateRenderer->render($mail->contentItem->getTemplateFieldValues()));
                $mail->contentItem->save();
            });
    }

    public function render(): View
    {
        return view('mailcoach::app.templates.edit')
            ->layout('mailcoach::app.layouts.app', [
                'title' => $this->template->name,
            ]);
    }
}
