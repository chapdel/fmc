<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class CampaignDelivery extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public Campaign $campaign;

    public ?CarbonInterface $scheduled_at_date;

    public array $scheduled_at;

    protected $listeners = [
        'send-campaign' => 'send',
    ];

    protected function rules(): array
    {
        return [
            'scheduled_at' => ['required', new DateTimeFieldRule()],
        ];
    }

    public function mount(Campaign $campaign): void
    {
        $this->authorize('send', $this->campaign);

        $this->campaign = $campaign;

        $this->scheduled_at_date = $campaign->scheduled_at ?? now()->setTimezone(config('app.timezone'))->addHour()->startOfHour();

        $this->scheduled_at = [
            'date' => $this->scheduled_at_date->format('Y-m-d'),
            'hours' => $this->scheduled_at_date->format('H'),
            'minutes' => $this->scheduled_at_date->format('i'),
        ];

        app(MainNavigation::class)->activeSection()?->add($campaign->name, route('mailcoach.campaigns.delivery', $campaign));
    }

    public function updatedScheduledAt()
    {
        $this->scheduled_at_date = (new DateTimeFieldRule())->parseDateTime($this->scheduled_at);
    }

    public function unschedule()
    {
        $this->campaign->markAsUnscheduled();

        $this->flash(__('mailcoach - Campaign :campaign was unscheduled', ['campaign' => $this->campaign->name]));
    }

    public function schedule()
    {
        $this->validate();

        if (! $this->campaign->isPending()) {
            $this->flash(__('mailcoach - Campaign :campaign could not be scheduled because it has already been sent.', ['campaign' => $this->campaign->name]), 'error');

            return;
        }

        $this->campaign->scheduleToBeSentAt($this->scheduled_at_date->setTimezone(config('app.timezone')));

        $this->flash(__('mailcoach - Campaign :campaign is scheduled for delivery.', ['campaign' => $this->campaign->name]));
    }

    public function send()
    {
        if (! $this->campaign->isPending()) {
            $this->flash(__('mailcoach - Campaign :campaign could not be sent because it has already been sent.', ['campaign' => $this->campaign->name]), 'error');

            return;
        }

        $this->campaign->send();

        flash()->success(__('mailcoach - Campaign :campaign is being sent.', ['campaign' => $this->campaign->name]));

        return redirect()->route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function render(): View
    {
        return view('mailcoach::app.campaigns.delivery', [
            'links' => $this->campaign->htmlLinks(),
        ])->layout('mailcoach::app.campaigns.layouts.campaign', [
            'campaign' => $this->campaign,
            'title' => __('mailcoach - Send'),
        ]);
    }
}
