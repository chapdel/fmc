<?php

namespace Spatie\Mailcoach\Http\App\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class StoreCampaignRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'name' => 'required',
            'email_list_id' => 'nullable',
            'type' => ['required', Rule::in([CampaignStatus::DRAFT, CampaignStatus::AUTOMATED])],
        ];
    }

    public function template(): Template
    {
        $templateClass = $this->getTemplateClass();

        return $templateClass::find($this->template_id) ?? new $templateClass();
    }
}
