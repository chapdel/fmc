<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

class ImportHasEmailHeaderAction
{
    public function execute(array $headers): bool
    {
        foreach ($this->potentialEmailKeys() as $key) {
            if (in_array($key, $headers, true)) {
                return true;
            }
        }

        return false;
    }

    protected function potentialEmailKeys(): array
    {
        return config('mailcoach.audience.imports.keys.emails', ['email', 'email address', 'Email']);
    }
}
