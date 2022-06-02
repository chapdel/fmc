<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class UnsubscribeUrlReplacer implements PersonalizedReplacer
{
    public function helpText(): array
    {
        return [
            'unsubscribeUrl' => __('mailcoach - The URL where users can unsubscribe'),
            'unsubscribeTag::your tag' => __('mailcoach - The URL where users can be removed from a specific tag'),
        ];
    }

    public function replace(string $text, Send $pendingSend): string
    {
        $unsubscribeUrl = $pendingSend->subscriber->unsubscribeUrl($pendingSend);

        $text = str_ireplace('::unsubscribeUrl::', $unsubscribeUrl, $text);
        $text = str_ireplace('%3A%3AunsubscribeUrl%3A%3A', $unsubscribeUrl, $text);

        $pattern = <<<REGEXP
            /
            (?:::|%3A%3A)                   # "::" or urlencoded "%3A%3A"
            unsubscribeTag(?:::|%3A%3A)     # "unsubscribeTag::" or urlencoded "unsubscribeTag%3A%3A"
            ((?!::|%3A%3A).*)               # Anything but "::" or "%3A%3A"
            (?:::|%3A%3A)                   # "::" or urlencoded "%3A%3A"
            /ix
        REGEXP;

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            [$key, $tag] = $match;

            $unsubscribeTagUrl = $pendingSend->subscriber->unsubscribeTagUrl(urldecode($tag), $pendingSend);

            $text = str_ireplace($key, $unsubscribeTagUrl, $text);
        }

        return $text;
    }
}
