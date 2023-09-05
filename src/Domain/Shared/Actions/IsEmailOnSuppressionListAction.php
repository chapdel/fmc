<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use InvalidArgumentException;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class IsEmailOnSuppressionListAction
{
    use UsesMailcoachModels;

    public function execute(string $email): bool
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The given email `{$email}` is invalid.");
        }

        return self::getSuppressionClass()::where('email', $email)->exists();
    }
}
