<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SegmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
        ];
    }
}
