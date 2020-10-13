<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray($resource)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,

            'email_list_id' => (int)$this->email_list_id,
            'email_list' => new EmailListResource($this->whenLoaded('emailList')),

            'segment' => new SegmentResource($this->whenLoaded('segment')),

            'from_email' => $this->from_email,
            'from_name' => $this->subject,

            'status' => $this->status,

            'html' => $this->html,
            'structured_html' => $this->structured_html,
            'email_html' => $this->email_html,
            'webview_html' => $this->webview_html,

            'mailable_class' => $this->mailable_class,

            'track_opens' => $this->track_opens,
            'track_clicks' => $this->track_clicks,

            'sent_to_number_of_subscribers' => $this->sent_to_number_of_subscribers,

            'segment_class' => $this->segment_cass,
            'segment_description' => $this->segment_description,
            'open_count' => $this->open_count,

            'unique_open_count' => $this->unique_open_count,
            'open_rate' => $this->open_rate / 100,
            'click_count' => $this->click_count,
            'unique_click_count' => $this->unique_click_count,
            'click_rate' => $this->click_rate / 100,
            'unsubscribe_count' => $this->unsubscribe_count,
            'unsubscribe_rate' => $this->unsubscribe_rate / 100,
            'bounce_count' => $this->bounce_count,
            'bounce_rate' => $this->bounce_rate / 100,

            'sent_at' => $this->sent_at,
            'statistics_calculated_at' => $this->statistics_calculated_at,
            'scheduled_at' => $this->scheduled_at,

            'last_modified_at' => $this->last_modified_at,

            'summary_mail_sent_at' => $this->summary_mail_sent_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
