<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignToSubscriberJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Automation\Models\Concerns\AutomationAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

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
