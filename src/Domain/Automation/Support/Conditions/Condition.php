<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

interface Condition
{
    public function __construct(Subscriber $subscriber, array $data);

    public static function getName(): string;

    public static function getDescription(array $data): string;

    public function check(): bool;
}
