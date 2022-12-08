<?php

declare(strict_types=1);

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;

class CustomSendDenyAllPolicy extends EmailListPolicy
{
    public function send(Authorizable $user): bool
    {
        return false;
    }

    public function create(Authorizable $user): bool
    {
        return false;
    }

    public function view(Authorizable $user, EmailList $list): bool
    {
        return false;
    }

    public function update(Authorizable $user, EmailList $list): bool
    {
        return false;
    }

    public function delete(Authorizable $user, EmailList $list): bool
    {
        return false;
    }
}
