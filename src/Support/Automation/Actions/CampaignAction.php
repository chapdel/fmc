<?php

namespace Spatie\Mailcoach\Support\Automation\Actions;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Jobs\SendCampaignToSubscriberJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignAction extends AutomationAction
{
    use SerializesModels, UsesMailcoachModels;

    public Campaign $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public static function getComponent(): ?string
    {
        return 'campaign-action';
    }

    public static function getName(): string
    {
        return __('Send a campaign');
    }

    public function getDescription(): string
    {
        return "{$this->campaign->name}";
    }

    public static function make(array $data): self
    {
        return new self(self::getCampaignClass()::find($data['campaign_id']));
    }

    public function toArray(): array
    {
        return [
            'campaign_id' => $this->campaign->id,
        ];
    }

    public function run(Subscriber $subscriber): void
    {
        if ($this->campaign->sends()->where('subscriber_id', $subscriber->id)->exists()) {
            return;
        }

        SendCampaignToSubscriberJob::dispatch($this->campaign, $subscriber);
    }
}
