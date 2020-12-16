<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer;

class PersonalizeSubjectAction
{
    public function execute(string $subject, Send $pendingSend): string
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $subject = str_ireplace('::sendUuid::', $pendingSend->uuid, $subject);
        $subject = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $subject);

        return collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->filter(fn (object $class) => $class instanceof PersonalizedReplacer)
            ->reduce(fn (string $subject, PersonalizedReplacer $replacer) => $replacer->replace($subject, $pendingSend), $subject);
    }
}
