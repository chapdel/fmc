<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

class AddUtmTagsToUrlAction
{
    public function execute(string $url, string $campaignName): string
    {
        $campaignName = urlencode($campaignName);
        $utmTags = "utm_source=newsletter&utm_medium=email&utm_campaign={$campaignName}";

        return $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . $utmTags;
    }
}
