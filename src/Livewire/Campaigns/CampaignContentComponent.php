<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class CampaignContentComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public Campaign $campaign;

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

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
        $this->subject = $campaign->subject;

        $this->authorize('update', $this->campaign);

        $this->templateOptions = self::getTemplateClass()::all()
            ->pluck('name', 'id')
            ->toArray();

        app(MainNavigation::class)->activeSection()->add($campaign->name, route('mailcoach.campaigns.content', $campaign));
    }

    public function save(): void
    {
        if (! $this->campaign->isEditable()) {
            $this->redirectRoute('mailcoach.campaigns.summary', $this->campaign);

            return;
        }

        $this->validate();

        $this->campaign->subject = $this->subject;
        $this->campaign->save();
    }

    public function render(): View
    {
        $this->mailer = $this->campaign->getMailerKey();

        return view($this->campaign->isEditable()
            ? 'mailcoach::app.campaigns.content'
            : 'mailcoach::app.campaigns.contentReadOnly'
        )->layout('mailcoach::app.campaigns.layouts.campaign', [
            'campaign' => $this->campaign,
            'title' => __mc('Content'),
        ]);
    }
}
