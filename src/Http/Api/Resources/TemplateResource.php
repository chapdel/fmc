<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Template\Models\Template */
class TemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'html' => $this->html,
            'fields' => $this->fields(),
            'structured_html' => $this->structured_html,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
