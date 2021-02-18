<?php

namespace Spatie\Mailcoach\Http\App\Requests\TransactionalMails;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class TransactionalMailTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => '',
            'subject' => '',
            'to' => new Delimited('email'),
            'cc' => new Delimited('email'),
            'bcc' => new Delimited('email'),
            'html' => '',
        ];
    }

    public function to(): array
    {
        return $this->delimitedToArray('to');
    }

    public function cc(): array
    {
        return $this->delimitedToArray('cc');
    }

    public function bcc(): array
    {
        return $this->delimitedToArray('bcc');
    }

    protected function delimitedToArray(string $name): array
    {
        $value = $this->get($name, '');

        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }
}
