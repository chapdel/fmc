<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;

class CustomAutomationMailReplacer implements AutomationMailReplacer
{
    public function helpText(): array
    {
        return [
            'customreplacer' => 'The custom replacer',
        ];
    }

    public function replace(string $text, AutomationMail $automationMail): string
    {
        return str_ireplace('::customreplacer::', 'The custom replacer works', $text);
    }
}
