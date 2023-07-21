<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Campaigns\Forms\CampaignSettingsForm;
use Spatie\Mailcoach\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class CampaignSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public Campaign $campaign;

    public CampaignSettingsForm $form;

    public Collection $emailLists;

    public Collection $segmentsData;

    public string $segment;

    public ?string $mailer;

    public function mount(Campaign $campaign): void
    {
        $this->authorize('update', $campaign);

        $this->campaign = $campaign;
        $this->form->setCampaign($campaign);
        $this->segment = $campaign->notSegmenting() ? 'entire_list' : 'segment';
        $this->mailer = $campaign->getMailerKey();

        $this->emailLists = self::getEmailListClass()::with('segments')->get();
        $this->segmentsData = $this->emailLists->map(fn (EmailList $emailList) => [
            'id' => $emailList->id,
            'name' => $emailList->name,
            'segments' => $emailList->segments()->orderBy('name')->pluck('name', 'id')->toArray(),
            'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
        ]);

        app(MainNavigation::class)->activeSection()?->add($campaign->name, route('mailcoach.campaigns.settings', $campaign));
    }

    public function save(): void
    {
        $this->form->save($this->segment);

        $this->flash(__mc('Campaign :campaign was updated.', ['campaign' => $this->form->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.campaigns.settings')
            ->layout('mailcoach::app.campaigns.layouts.campaign', [
                'campaign' => $this->campaign,
                'title' => __mc('Settings'),
            ]);
    }
}
