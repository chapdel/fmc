<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @todo can be removed ? */
/** @mixin \Spatie\Mailcoach\Domain\Audience\Models\TagSegment */
class SegmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email_list' => $this->whenLoaded('emailList', function () {
                return EmailListResource::make($this->emailList);
            }),
            'email_list_uuid' => $this->emailList->uuid,
            'all_positive_tags_required' => $this->all_positive_tags_required,
            'all_negative_tags_required' => $this->all_negative_tags_required,
            'positive_tags' => $this->positiveTags->pluck('name'),
            'negative_tags' => $this->negativeTags->pluck('name'),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
