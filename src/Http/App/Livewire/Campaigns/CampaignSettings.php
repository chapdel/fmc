<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class CampaignSettings extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public Campaign $campaign;

    public Collection $emailLists;

    public Collection $segmentsData;

    public string $segment;

    protected function rules(): array
    {
        return [
            'campaign.name' => 'required',
            'campaign.subject' => '',
            'campaign.email_list_id' => Rule::exists(self::getEmailListTableName(), 'id'),
            'campaign.track_opens' => 'bool',
            'campaign.track_clicks' => 'bool',
            'campaign.utm_tags' => 'bool',
            'campaign.segment_id' => ['required_if:segment,segment'],
            'segment' => [Rule::in(['entire_list', 'segment'])],
        ];
    }

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;

        $this->authorize('update', $this->campaign);

        $this->emailLists = self::getEmailListClass()::with('segments')->get();
        $this->segmentsData = $this->emailLists->map(fn (EmailList $emailList) => [
            'id' => $emailList->id,
            'name' => $emailList->name,
            'segments' => $emailList->segments->map->only('id', 'name'),
            'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
        ]);

        $this->segment = $this->campaign->notSegmenting() ? 'entire_list' : 'segment';
    }

    public function save(): void
    {
        $this->validate();

        $segmentClass = SubscribersWithTagsSegment::class;

        if ($this->segment === 'entire_list') {
            $segmentClass = EverySubscriberSegment::class;
        }

        if ($this->campaign->usingCustomSegment()) {
            $segmentClass = $this->campaign->segment_class;
        }

        $this->campaign->fill([
            'last_modified_at' => now(),
            'segment_class' => $segmentClass,
            'segment_id' => $segmentClass === EverySubscriberSegment::class
                ? null
                : $this->campaign->segment_id,
        ]);

        $this->campaign->save();

        $this->campaign->update(['segment_description' => $this->campaign->getSegment()->description()]);

        $this->flash(__('mailcoach - Campaign :campaign was updated.', ['campaign' => $this->campaign->name]), 'error');
    }

    public function render(): View
    {
        return view('mailcoach::app.campaigns.settings')
            ->layout('mailcoach::app.campaigns.layouts.campaign', [
                'campaign' => $this->campaign,
                'title' => __('mailcoach - Settings'),
            ]);
    }
}
