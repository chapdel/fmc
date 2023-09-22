<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

abstract class ContentItemTable extends TableComponent
{
    public ContentItem $contentItem;

    public function mount()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Sendable $sendable */
        $sendable = Route::current()->parameter('campaign')
            ?? Route::current()->parameter('automationMail');

        $this->contentItem = $sendable->contentItem;

        app(MainNavigation::class)->activeSection()?->add($this->contentItem->model->name, match (true) {
            $this->contentItem->model instanceof Campaign => route('mailcoach.campaigns'),
            $this->contentItem->model instanceof AutomationMail => route('mailcoach.automations.mails'),
            default => '',
        });
    }

    public function getLayout(): string
    {
        return match (true) {
            $this->contentItem->model instanceof Campaign => 'mailcoach::app.campaigns.layouts.campaign',
            $this->contentItem->model instanceof AutomationMail => 'mailcoach::app.automations.mails.layouts.automationMail',
            default => '',
        };
    }

    public function getLayoutData(): array
    {
        return [
            'campaign' => $this->contentItem->model,
            'mail' => $this->contentItem->model,
        ];
    }
}
