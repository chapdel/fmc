<?php

namespace Spatie\Mailcoach\Exceptions;

use Exception;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;
use Spatie\Mailcoach\Support\Segments\Segment;

class CouldNotSendCampaign extends Exception
{
    public static function beingSent(CampaignConcern $campaign): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent, because it is already being sent.");
    }

    public static function invalidMailableClass(CampaignConcern $campaign, string $invalidMailableClass): self
    {
        $mustExtend = CampaignMail::class;

        return new static("The campaign with id `{$campaign->id}` can't be sent, because an invalid mailable class `{$invalidMailableClass}` is set. A valid mailable class must extend `{$mustExtend}`.");
    }

    public static function invalidSegmentClass(CampaignConcern $campaign, string $invalidSegmentClass): self
    {
        $mustExtend = Segment::class;

        return new static("The campaign with id `{$campaign->id}` can't be sent, because an invalid segment class `{$invalidSegmentClass}` is set. A valid segment class must implement `{$mustExtend}`.");
    }

    public static function alreadySent(CampaignConcern $campaign): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent, because it was already sent.");
    }

    public static function noListSet(CampaignConcern $campaign): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent, because there is no list set to send it to.");
    }

    public static function noSubjectSet(CampaignConcern $campaign): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent, because it has no subject.");
    }

    public static function noContent(CampaignConcern $campaign): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent because it has no content.");
    }

    public static function invalidContent(CampaignConcern $campaign, Exception $errorException): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent because the content isn't valid. Please check if the html is valid. DOMDocument reported: `{$errorException->getMessage()}`", 0, $errorException);
    }

    public static function noFromEmailSet(CampaignConcern $campaign): self
    {
        return new static("The campaign with id `{$campaign->id}` can't be sent because it has no no from email.");
    }
}
