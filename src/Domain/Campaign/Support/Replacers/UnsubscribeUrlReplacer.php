<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;

class UnsubscribeUrlReplacer implements PersonalizedReplacer
{
    public function helpText(): array
    {
        return [
            'unsubscribeUrl' => __('The URL where users can unsubscribe'),
        ];
    }

    public function replace(string $text, Send $pendingSend): string
    {
        $unsubscribeUrl = $pendingSend->subscriber->unsubscribeUrl($pendingSend);

        return str_ireplace('::unsubscribeUrl::', $unsubscribeUrl, $text);
    }
}
