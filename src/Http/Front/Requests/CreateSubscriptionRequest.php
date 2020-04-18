<?php

namespace Spatie\Mailcoach\Http\Front\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateSubscriptionRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules()
    {
        return [
            'email' => ['required', 'email:rfc,dns'],
            'first_name' => '',
            'last_name' => '',
            'redirect_after_subscribed' => '',
            'redirect_after_already_subscribed' => '',
            'redirect_after_subscription_pending',
            'tags' => '',
        ];
    }

    public function subscriberAttributes(): array
    {
        return Arr::except($this->validated(), [
            'email',
            'redirect_after_subscribed',
            'redirect_after_already_subscribed',
            'redirect_after_subscription_pending',
            'tags',
        ]);
    }

    public function tags(): array
    {
        $tags = explode(';', $this->tags);

        $tags = array_map('trim', $tags);

        $allowedEmailListTags = $this->emailList()->allowedFormSubscriptionTags()->pluck('name')->toArray();

        $tags = array_filter($tags, fn (string $tag) => in_array($tag, $allowedEmailListTags));

        return array_filter($tags);
    }

    public function emailList(): ?EmailList
    {
        return $this->getEmailListClass()::query()
            ->where('uuid', $this->route()->parameter('emailListUuid'))
            ->where('allow_form_subscriptions', true)
            ->first();
    }
}
