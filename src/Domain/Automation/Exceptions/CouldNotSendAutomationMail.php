<?php

namespace Spatie\Mailcoach\Domain\Automation\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Segments\Segment;

class CouldNotSendAutomationMail extends Exception
{
    public static function invalidContent(AutomationMail $automationMail, Exception $errorException): self
    {
        return new static("The automation mail with id `{$automationMail->id}` can't be sent because the content isn't valid. Please check if the html is valid. DOMDocument reported: `{$errorException->getMessage()}`", 0, $errorException);
    }
}
