<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateSubscriberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', $this->getUniqueRule()],
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'tags' => 'array',
        ];
    }

    public function subscriberAttributes()
    {
        return Arr::except($this->validated(), ['tags']);
    }

    protected function getUniqueRule(): Unique
    {
        $emailList = $this->route('emailList');

        $rule = Rule::unique('mailcoach_subscribers', 'email')->where('email_list_id', $emailList->id);

        $subscriber = $this->route('subscriber');

        $rule->ignore($subscriber->id);

        return $rule;
    }

    public function messages()
    {
        return [
            'email.unique' => 'There already is a subscriber on this list with this email.',
        ];
    }
}
