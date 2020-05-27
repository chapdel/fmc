<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Models\Template;

class StoreCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email_list_id' => 'nullable',
        ];
    }

    public function template(): Template
    {
        return Template::find($this->template_id) ?? new Template();
    }
}
