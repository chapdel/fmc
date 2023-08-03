<?php

declare(strict_types=1);

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Policies\SubscriberImportPolicy;

class CustomSubscriberImportDenyAllPolicy extends SubscriberImportPolicy
{
    public function create(Authorizable $user): bool
    {
        return false;
    }

    public function view(Authorizable $user, SubscriberImport $import): bool
    {
        return false;
    }

    public function update(Authorizable $user, SubscriberImport $import): bool
    {
        return false;
    }

    public function delete(Authorizable $user, SubscriberImport $import): bool
    {
        return false;
    }

    public function start(Authorizable $user, SubscriberImport $import): bool
    {
        return false;
    }
}
