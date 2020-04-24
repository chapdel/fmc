<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class UpdateCampaignSettingsRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'name' => 'required',
            'subject' => '',
            'email_list_id' => 'exists:mailcoach_email_lists,id',
            'track_opens' => 'bool',
            'track_clicks' => 'bool',
            'segment' => [Rule::in(['entire_list', 'segment'])],
            'segment_id' => ['required_if:segment,tag_segment'],
        ];
    }

    public function getSegmentClass(): string
    {
        /** @var \Spatie\Mailcoach\Models\\Concerns\Campaign $campaign */
        $campaign = $this->route()->parameter('campaign');

        if ($campaign->usingCustomSegment()) {
            return $campaign->segment_class;
        }

        if ($this->segment === 'entire_list') {
            return EverySubscriberSegment::class;
        }

        return SubscribersWithTagsSegment::class;
    }

    public function emailList(): ?EmailList
    {
        if (! $this->email_list_id) {
            return null;
        }

        return $emailList = $this->getEmailListClass()::find($this->email_list_id);
    }
}
