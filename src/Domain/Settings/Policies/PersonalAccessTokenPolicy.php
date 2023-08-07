<?php

namespace Spatie\Mailcoach\Domain\Settings\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Mailcoach\Domain\Settings\Models\MailcoachUser;
use Spatie\Mailcoach\Domain\Settings\Models\PersonalAccessToken;

class PersonalAccessTokenPolicy
{
    use HandlesAuthorization;

    public function administer(MailcoachUser $user, PersonalAccessToken $personalAccessToken)
    {
        return $user->id === $personalAccessToken->user()->id;
    }
}
