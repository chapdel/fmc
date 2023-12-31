<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class ValidateCampaignRequirementsAction
{
    /**
     * @return string[] An array of error messages, return an empty array when everything is okay!
     */
    public function execute(Campaign $campaign): array
    {
        return [];
    }
}
