<?php

namespace Spatie\Mailcoach\Mails\Concerns;

use Spatie\Mailcoach\Support\Replacers\Concerns\ReplacesModelAttributes;

trait ReplacesPlaceholders
{
    use ReplacesModelAttributes;

    public function replacePlaceholders(string $text): string
    {
        $text = $this->replaceModelAttributes($text, 'subscriber', $this->subscriber);

        $text = $this->replaceModelAttributes($text, 'list', $this->subscriber->emailList);

        return $text;
    }
}
