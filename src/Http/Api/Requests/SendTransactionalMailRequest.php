<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Settings\Rules\MailerConfigKeyNameRule;
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
            'replacements' => ['array'],
            'fields' => ['array'],
            'from' => ['required'],
            'to' => ['required', (new Delimited('email'))->min(1)],
            'cc' => ['nullable', (new Delimited('email'))->min(1)],
            'bcc' => ['nullable', (new Delimited('email'))->min(1)],
            'store' => ['boolean'],

            'mailer' => ['string', new MailerConfigKeyNameRule()],
        ];
    }

    public function replacements(): array
    {
        return $this->replacements ?? [];
    }

    public function fields(): array
    {
        return $this->fields ?? [];
    }

    public function shouldStoreMail(): bool
    {
        return (bool)$this->store ?? true;
    }
}
