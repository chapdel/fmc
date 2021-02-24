<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class HasTagCondition implements Condition
{
    public function __construct(
        private Subscriber $subscriber,
        private array $data,
    ) {}

    public static function getName(): string
    {
        return __('Has tag');
    }

    public static function getDescription(array $data): string
    {
        return __(':tag', ['tag' => $data['tag']]);
    }

    public static function rules(): array
    {
        return [
            'tag' => 'required',
        ];
    }

    public function check(): bool
    {
        return $this->subscriber->hasTag($this->data['tag']);
    }
}
