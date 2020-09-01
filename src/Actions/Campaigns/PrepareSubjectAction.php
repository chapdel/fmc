<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Replacer;

class PrepareSubjectAction
{
    public function execute(Campaign $campaign)
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
