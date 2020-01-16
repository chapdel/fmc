<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSubscribersRequest extends FormRequest
{
    public function rules()
    {
        return [
             // 'file' => 'file:csv,xlsx',
        ];
    }
}
