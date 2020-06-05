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
            'list.name' => __('The name of the email list this campaign is sent to'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        return $this->replaceModelAttributes($text, 'list', $campaign->emailList);
    }
}
