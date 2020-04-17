<?php

namespace Spatie\Mailcoach\Exceptions;

use Exception;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CouldNotUpdateCampaign extends Exception
{
    public static function beingSent(CampaignConcern $campaign): self
    {
        return new static("The campaign `{$campaign->name}` cannot be updated because it is being sent.");
    }

    public static function alreadySent(CampaignConcern $campaign): self
    {
        return new static("The campaign `{$campaign->name}` cannot be updated because it was already sent.");
    }
}
