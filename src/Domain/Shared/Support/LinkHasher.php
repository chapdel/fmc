<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class LinkHasher
{
    public static function hash(Sendable $sendable, string $url, string $type = 'clicked'): string
    {
        $prefix = match($sendable::class) {
            Campaign::class => "campaign",
            AutomationMail::class => "automation-mail",
        };

        $sendablePart = "{$prefix}-{$sendable->id}-{$type}";

        $humanReadablePart = self::getHumanReadablePart($url);

        $randomPart = substr(md5($url), 0, 8);

        return "{$sendablePart}-{$humanReadablePart}-{$randomPart}";
    }

    protected static function getHumanReadablePart(string $url)
    {
        $url = Str::after($url, '://');

        $slug = str_replace(['.'], '-', $url);

        $slug = Str::slug($slug);

        return Str::limit($slug, 30, '');
    }
}
