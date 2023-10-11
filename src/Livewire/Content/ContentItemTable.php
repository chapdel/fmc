<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\Concerns\HasContentItems;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

abstract class ContentItemTable extends TableComponent
{
    /** @var \Illuminate\Support\Collection<ContentItem> */
    public Collection $contentItems;

    public HasContentItems $model;

    public function mount()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Sendable $sendable */
        $sendable = Route::current()->parameter('campaign')
            ?? Route::current()->parameter('automationMail');

        if (is_string($sendable)) {
            $sendable = self::getCampaignClass()::findByUuid($sendable)
                ?? self::getAutomationMailClass()::findByUuid($sendable);
        }

        $this->model = $sendable;
        $this->contentItems = $this->model->contentItems;

        app(MainNavigation::class)->activeSection()?->add($this->model->name, match (true) {
            $this->model instanceof Campaign => route('mailcoach.campaigns'),
            $this->model instanceof AutomationMail => route('mailcoach.automations.mails'),
            default => '',
        });
    }

    public function getLayout(): string
    {
        return match (true) {
            $this->model instanceof Campaign => 'mailcoach::app.campaigns.layouts.campaign',
            $this->model instanceof AutomationMail => 'mailcoach::app.automations.mails.layouts.automationMail',
            default => '',
        };
    }

    public function getLayoutData(): array
    {
        return [
            'campaign' => $this->model,
            'mail' => $this->model,
        ];
    }
}
