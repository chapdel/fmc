<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeHtmlAction
{
    public function execute($html, Send $pendingSend): string
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $html = str_ireplace('::sendUuid::', $pendingSend->uuid, $html);
        $html = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $html);

        return $pendingSend->campaign->getPersonalizedReplacers()
            ->reduce(fn (string $html, PersonalizedReplacer $replacer) => $replacer->replace($html, $pendingSend), $html);
    }
}
