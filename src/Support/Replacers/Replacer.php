<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

interface Replacer extends ReplacerWithHelpText
{
    public function replace(string $text, CampaignConcern $campaign): string;
}
