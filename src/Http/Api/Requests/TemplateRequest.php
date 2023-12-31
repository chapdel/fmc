<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'html' => '',
            'structured_html' => 'nullable',
        ];
    }
}
