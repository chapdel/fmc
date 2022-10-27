<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class ReplacePlaceholdersAction
{
    public function execute(?string $text, Sendable $sendable): string
    {
        $text ??= '';

        return match (true) {
            $sendable instanceof Campaign => $sendable->getReplacers()
                ->reduce(fn (string $text, CampaignReplacer $replacer) => $replacer->replace($text, $sendable), $text),
            $sendable instanceof AutomationMail => $sendable->getReplacers()
                ->reduce(fn (string $text, AutomationMailReplacer $replacer) => $replacer->replace($text, $sendable), $text),
            default => $text,
        };
    }
}
