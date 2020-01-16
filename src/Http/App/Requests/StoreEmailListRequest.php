<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'default_from_email' => 'email:rfc',
            'default_from_name' => '',
        ];
    }
}
