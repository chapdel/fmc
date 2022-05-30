<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ValidationRules\Rules\Delimited;

class SendTransactionalMailRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'template' => ['required', 'string', Rule::exists(self::getTransactionalMailTemplateTableName(), 'name')],
            'subject' => ['required', 'string'],
            'from' => ['required'],
            'to' => ['required', (new Delimited('email'))->min(1)],
            'cc' => ['nullable', (new Delimited('email'))->min(1)],
            'bcc' => ['nullable', (new Delimited('email'))->min(1)],
            'track_opens' => ['nullable', 'bool'],
            'track_clicks' => ['nullable', 'bool'],
        ];
    }
}
