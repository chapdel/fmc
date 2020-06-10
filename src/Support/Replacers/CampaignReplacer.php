<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Concerns\ReplacesModelAttributes;

class CampaignReplacer implements Replacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'campaign.name' => __('The name of this campaign'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        return $this->replaceModelAttributes($text, 'campaign', $campaign);
    }
}
