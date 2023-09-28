<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class EditContentComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public ContentItem $contentItem;

    public ?string $subject;

    public array $templateOptions;

    public ?string $mailer;

    protected $listeners = [
        'editorSaved' => 'save',
        'editorSavedQuietly' => 'save',
    ];

    protected function rules(): array
    {
        return [
            'subject' => ['nullable', 'string'],
        ];
    }

    public function mount(): void
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Sendable $sendable */
        $sendable = Route::current()->parameter('campaign') ?? Route::current()->parameter('automationMail');

        if (is_string($sendable)) {
            $sendable = self::getCampaignClass()::findByUuid($sendable)
                ?? self::getAutomationMailClass()::findByUuid($sendable);
        }

        $this->contentItem = $sendable->contentItem;
        $this->subject = $this->contentItem->subject;

        $this->authorize('update', $this->contentItem->model);

        $this->templateOptions = self::getTemplateClass()::all()
            ->pluck('name', 'id')
            ->toArray();

        app(MainNavigation::class)->activeSection()?->add($this->contentItem->model->name, match (true) {
            $this->contentItem->model instanceof Campaign => route('mailcoach.campaigns.content', $this->contentItem->model),
            $this->contentItem->model instanceof AutomationMail => route('mailcoach.automations.mails.content', $this->contentItem->model),
            default => '',
        });
    }

    public function save(): void
    {
        $this->validate();

        $this->contentItem->subject = $this->subject;
        $this->contentItem->save();
    }

    public function render(): View
    {
        $this->mailer = $this->contentItem->getMailerKey();

        $view = $this->contentItem->model->isEditable()
            ? 'mailcoach::app.content.edit'
            : 'mailcoach::app.content.view';

        $layout = match (true) {
            $this->contentItem->model instanceof Campaign => 'mailcoach::app.campaigns.layouts.campaign',
            $this->contentItem->model instanceof AutomationMail => 'mailcoach::app.automations.mails.layouts.automationMail',
            default => '',
        };

        return view($view)->layout($layout, [
            'campaign' => $this->contentItem->model,
            'mail' => $this->contentItem->model,
            'title' => __mc('Content'),
        ]);
    }
}
