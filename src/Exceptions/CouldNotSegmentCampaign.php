<?php

namespace Spatie\Mailcoach\Exceptions;

use Exception;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class CouldNotSegmentCampaign extends Exception
{
    public static function emailListNotSet(CampaignConcern $campaign): self
    {
        return new static("Could not segment campaign `$campaign->name` because no list was be set. You must set a list before segmenting on subscribers.");
    }

    public static function tagDoesNotExistOnTheEmailList(string $tag, CampaignConcern $campaign): self
    {
        return new static("Could not segment campaign `$campaign->name` because the specified tag `{$tag}` does not exist on list `{$campaign->emailList->name}`.");
    }

    public static function noTagsSet(CampaignConcern $campaign): self
    {
        return new static("Could not segment campaign `$campaign->name` because no tags to segment on have been set.");
    }
}
