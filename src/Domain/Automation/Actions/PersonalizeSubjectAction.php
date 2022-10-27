<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Support\Replacers\PersonalizedReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeSubjectAction
{
    public function execute(string $subject, Send $pendingSend): string
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $subject = str_ireplace('::sendUuid::', $pendingSend->uuid, $subject);
        $subject = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $subject);

        return $pendingSend->automationMail->getPersonalizedReplacers()
            ->reduce(fn (string $subject, PersonalizedReplacer $replacer) => $replacer->replace($subject, $pendingSend), $subject);
    }
}
