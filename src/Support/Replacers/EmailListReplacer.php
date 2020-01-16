<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Concerns\ReplacesModelAttributes;

class EmailListReplacer implements Replacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'list.name' =>  'The name of the email list this campaign is sent to',
        ];
    }

    public function replace(string $html, Campaign $campaign): string
    {
        return $this->replaceModelAttributes($html, 'list', $campaign->emailList);
    }
}
