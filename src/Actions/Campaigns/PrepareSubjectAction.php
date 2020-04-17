<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;
use Spatie\Mailcoach\Support\Replacers\Replacer;

class PrepareSubjectAction
{
    public function execute(CampaignConcern $campaign)
    {
        $this->replacePlaceholdersInSubject($campaign);

        $campaign->save();
    }

    protected function replacePlaceholdersInSubject(CampaignConcern $campaign): void
    {
        $campaign->subject = collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->filter(fn (object $class) => $class instanceof Replacer)
            ->reduce(fn (string $subject, Replacer $replacer) => $replacer->replace($subject, $campaign), $campaign->subject);
    }
}
