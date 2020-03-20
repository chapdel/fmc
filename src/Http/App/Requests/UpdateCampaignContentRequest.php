<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Rules\HtmlRule;

class UpdateCampaignContentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'html' => ['required', new HtmlRule()],
            'structured_html' => ['nullable'],
        ];
    }
}
