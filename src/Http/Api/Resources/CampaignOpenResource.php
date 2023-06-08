<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;

/** @mixin CampaignOpen */
class CampaignOpenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'subscriber_uuid' => $this->subscriber_uuid, // @todo should be subscriber_id
            'subscriber_email_list_uuid' => (int) $this->subscriber_email_list_uuid, // @todo does not exist?
            'subscriber_email' => $this->subscriber_email, // @todo does not exist?
            'open_count' => (int) $this->open_count, // @todo does not exist?
            'first_opened_at' => $this->first_opened_at, // @todo does not exist?
        ];
    }
}
