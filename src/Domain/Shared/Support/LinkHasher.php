<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class LinkHasher
{
    public static function hash(Campaign $campaign, string $url, string $type = 'clicked'): string
    {
        $campaignPart = "campaign-{$campaign->id}-{$type}";

        $humanReadablePart = self::getHumanReadablePart($url);

        $randomPart = substr(md5($url), 0, 8);

        return "{$campaignPart}-{$humanReadablePart}-{$randomPart}";
    }

    protected static function getHumanReadablePart(string $url)
    {
        $url = Str::after($url, '://');

        $slug = str_replace(['.'], '-', $url);

        $slug = Str::slug($slug);

        return Str::limit($slug, 30, '');
    }
}
