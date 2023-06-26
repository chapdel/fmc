<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Shared\Models\Send */
class SendResource extends JsonResource
{
    public function toArray($request)
    {
        $opens = match (true) {
            $this->concernsCampaign() => $this->opens->count(),
            $this->concernsTransactionalMail() => $this->transactionalMailOpens->count(),
            $this->concernsAutomationMail() => $this->automationMailOpens->count(),
            default => null,
        };

        $clicks = match (true) {
            $this->concernsCampaign() => $this->clicks->count(),
            $this->concernsTransactionalMail() => $this->transactionalMailClicks->count(),
            $this->concernsAutomationMail() => $this->automationMailClicks->count(),
            default => null,
        };

        return [
            'uuid' => $this->uuid,
            'transport_message_id' => $this->transport_message_id,
            'campaign_uuid' => $this->campaign?->uuid,
            'automation_mail_uuid' => $this->automationMail?->uuid,
            'transactional_mail_log_item_uuid' => $this->transactionalMailLogItem?->uuid,
            'subscriber_uuid' => $this->subscriber?->uuid,
            'sent_at' => $this->sent_at,
            'failed_at' => $this->failed_at,
            'failure_reason' => $this->failure_reason,
            'open_count' => $opens,
            'click_count' => $clicks,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
