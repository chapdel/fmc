<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;

class PrepareSubjectAction
{
    public function execute(AutomationMail $automationMail): void
    {
        $this->replacePlaceholdersInSubject($automationMail);

        $automationMail->save();
    }

    protected function replacePlaceholdersInSubject(AutomationMail $automationMail): void
    {
        $automationMail->subject = $automationMail->getReplacers()
            ->reduce(fn (string $subject, AutomationMailReplacer $replacer) => $replacer->replace($subject, $automationMail), $automationMail->subject);
    }
}
