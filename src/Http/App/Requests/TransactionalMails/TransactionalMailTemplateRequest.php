<?php

namespace Spatie\Mailcoach\Http\App\Requests\TransactionalMails;

use Illuminate\Foundation\Http\FormRequest;

class TransactionalMailTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => '',
            'subject' => '',
            'html' => '',
        ];
    }
}
