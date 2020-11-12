<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists;

use Illuminate\Foundation\Http\FormRequest;

class ImportSubscribersRequest extends FormRequest
{
    public function rules()
    {
        return [
            // 'file' => 'file:csv,xlsx',
            'replace_tags' => 'boolean',
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

    public function replaceTags(): bool
    {
        return $this->has('replace_tags');
    }
}
