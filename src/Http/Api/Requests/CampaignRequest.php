<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules()
    {
        return [
            'name' => ['required'],
            'type' => ['nullable', Rule::in([CampaignStatus::Draft->value])],
            'email_list_uuid' => ['required', Rule::exists(self::getEmailListTableName(), 'uuid')],
            'segment_uuid' => ['nullable', Rule::exists(self::getTagSegmentTableName(), 'uuid')],
            'html' => '',
            'mailable_class' => '',
            'utm_tags' => 'boolean',
            'schedule_at' => 'date_format:Y-m-d H:i:s',
        ];
    }

    public function template(): Template
    {
        $templateClass = self::getTemplateClass();

        return $templateClass::findByUuid($this->template_uuid) ?? new $templateClass();
    }
}
