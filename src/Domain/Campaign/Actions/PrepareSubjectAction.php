<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Replacer;

class PrepareSubjectAction
{
    public function execute(Campaign $campaign): void
    {
        $this->replacePlaceholdersInSubject($campaign);

        $campaign->save();
    }

    protected function replacePlaceholdersInSubject(Campaign $campaign): void
    {
        $campaign->subject = collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->filter(fn (object $class) => $class instanceof Replacer)
            ->reduce(fn (string $subject, Replacer $replacer) => $replacer->replace($subject, $campaign), $campaign->subject);
    }
}
