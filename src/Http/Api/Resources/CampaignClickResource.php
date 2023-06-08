<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick;

/** @mixin CampaignClick */
class CampaignClickResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid, // @todo should be id?
            'url' => $this->url, // @todo not in db?
            'unique_click_count' => $this->unique_click_count, // @todo where is this from?
            'click_count' => $this->click_count,
        ];
    }
}
