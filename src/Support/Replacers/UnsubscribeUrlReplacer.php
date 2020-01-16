<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Send;

class UnsubscribeUrlReplacer implements PersonalizedReplacer
{
    public function helpText(): array
    {
        return [
            'unsubscribeUrl' => 'The URL where users can unsubscribe',
        ];
    }

    public function replace(string $html, Send $pendingSend): string
    {
        $unsubscribeUrl = $pendingSend->subscriber->unsubscribeUrl($pendingSend);

        return str_ireplace('::unsubscribeUrl::', $unsubscribeUrl, $html);
    }
}
