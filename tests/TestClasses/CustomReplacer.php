<?php


namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Replacer;

class CustomReplacer implements Replacer
{
    public function helpText(): array
    {
        return [
            'customreplacer' => 'The custom replacer',
        ];
    }

    public function replace(string $html, Campaign $campaign): string
    {
        return str_ireplace('::customreplacer::', "The custom replacer works", $html);
    }
}
