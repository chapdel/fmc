<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class WebviewReplacer implements Replacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => 'This URL will display the HTML of the campaign',
        ];
    }

    public function replace(string $text, CampaignConcern $campaign): string
    {
        $webviewUrl = $campaign->webviewUrl();

        return str_ireplace('::webviewUrl::', $webviewUrl, $text);
    }
}
