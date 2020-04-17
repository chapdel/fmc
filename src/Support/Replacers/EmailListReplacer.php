<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;
use Spatie\Mailcoach\Support\Replacers\Concerns\ReplacesModelAttributes;

class EmailListReplacer implements Replacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'list.name' => 'The name of the email list this campaign is sent to',
        ];
    }

    public function replace(string $text, CampaignConcern $campaign): string
    {
        return $this->replaceModelAttributes($text, 'list', $campaign->emailList);
    }
}
