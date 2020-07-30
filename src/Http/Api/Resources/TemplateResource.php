<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'html' => $this->html,
            'structured_html' => $this->structured_html,
        ];
    }
}
