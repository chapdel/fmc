<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Models\TagSegment;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CampaignRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules()
    {
        return [
            'name' => ['required'],
            'email_list_id' => ['required'],

            /* TODO: figure out why this validation is not working */
            // 'email_list_id' => ['required', 'exists:mailcoach_email_lists,id'],
            'segment_id' => [Rule::exists((new TagSegment())->getTable())],
            'html' => '',
            'mailable_class' => '',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'schedule_at' => 'date_format:Y-m-d H:i:s',
        ];
    }

    public function template(): Template
    {
        $templateClass = $this->getTemplateClass();

        return $templateClass::find($this->template_id) ?? new $templateClass();
    }
}
