<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer;

class PersonalizeHtmlAction
{
    public function execute($html, Send $pendingSend): string
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $html = str_ireplace('::sendUuid::', $pendingSend->uuid, $html);
        $html = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $html);

        return collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->filter(fn (object $class) => $class instanceof PersonalizedReplacer)
            ->reduce(fn (string $html, PersonalizedReplacer $replacer) => $replacer->replace($html, $pendingSend), $html);
    }
}
