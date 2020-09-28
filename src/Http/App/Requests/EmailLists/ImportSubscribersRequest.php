<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists;

use Illuminate\Foundation\Http\FormRequest;

class ImportSubscribersRequest extends FormRequest
{
    public function rules()
    {
        return [
            // 'file' => 'file:csv,xlsx',
        ];
    }

    public function subscribeUnsubscribed(): bool
    {
        return $this->has('subscribe_unsubscribed');
    }

    public function unsubscribeMissing(): bool
    {
        return $this->has('unsubscribe_others');
    }
}
