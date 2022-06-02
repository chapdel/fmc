<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class WebviewCampaignReplacer implements CampaignReplacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => __('mailcoach - This URL will display the HTML of the campaign'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        $webviewUrl = $campaign->webviewUrl();

        $text = str_ireplace('::webviewUrl::', $webviewUrl, $text);
        $text = str_ireplace(urlencode('::webviewUrl::'), $webviewUrl, $text);

        return $text;
    }
}
