<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email_list_id' => $this->email_list_id,

            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'extra_attributes' => $this->extra_attributes,

            'uuid' => $this->uuid,
            'subscribed_at' => $this->subscribed_at,
            'unsubscribed_at' => $this->unsubscribed_at,
        ];
    }
}
