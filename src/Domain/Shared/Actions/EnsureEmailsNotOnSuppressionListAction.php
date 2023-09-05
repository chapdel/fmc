<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use InvalidArgumentException;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\SuppressedEmail;

class EnsureEmailsNotOnSuppressionListAction
{
    use UsesMailcoachModels;

    public function execute(string|array $email): void
    {
        if (is_array($email)) {
            $this->ensureMultipleEmails($email);

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The given email `{$email}` is invalid.");
        }

        if (self::getSuppressionClass()::where('email', $email)->exists()) {
            throw SuppressedEmail::make($email);
        }
    }

    protected function ensureMultipleEmails(array $emails): void
    {
        $firstSuppressed = self::getSuppressionClass()::query()
            ->whereIn('email', $emails)
            ->first();

        if ($firstSuppressed) {
            throw SuppressedEmail::make($firstSuppressed->email);
        }
    }
}
