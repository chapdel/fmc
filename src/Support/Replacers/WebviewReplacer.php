<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Campaign;

class WebviewReplacer implements Replacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => __('This URL will display the HTML of the campaign'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        $webviewUrl = $campaign->webviewUrl();

        return str_ireplace('::webviewUrl::', $webviewUrl, $text);
    }
}
