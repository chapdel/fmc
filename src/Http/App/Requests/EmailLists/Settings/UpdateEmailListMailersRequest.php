<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

class UpdateEmailListMailersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'campaign_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'transactional_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
        ];
    }
}
