<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Support\Replacers\Concerns\ReplacesModelAttributes;

class SubscriberReplacer implements PersonalizedReplacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'subscriber.first_name' => __('The first name of the subscriber'),
            'subscriber.email' => __('The email of the subscriber'),
        ];
    }

    public function replace(string $text, Send $send): string
    {
        $subscriber = $send->subscriber;

        return $this->replaceModelAttributes($text, 'subscriber', $subscriber);
    }
}
