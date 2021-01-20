<?php
declare(strict_types=1);

namespace Spatie\Mailcoach\Tests\TestClasses;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Policies\CampaignPolicy;

class CustomCampaignDenyAllPolicy extends CampaignPolicy
{
    public function create(Authorizable $user): bool
    {
        return false;
    }

    public function view(Authorizable $user, Campaign $campaign): bool
    {
        return false;
    }

    public function update(Authorizable $user, Campaign $campaign): bool
    {
        return false;
    }

    public function delete(Authorizable $user, Campaign $campaign): bool
    {
        return false;
    }
}
