<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Campaign;

interface Replacer extends ReplacerWithHelpText
{
    public function replace(string $html, Campaign $campaign): string;
}
