<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\PrepareEmailHtmlAction as BasePrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\ReplacePlaceholdersAction;
use Throwable;

class PrepareEmailHtmlAction extends BasePrepareEmailHtmlAction
{

}
