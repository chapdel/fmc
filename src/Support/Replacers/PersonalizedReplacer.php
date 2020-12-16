<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Send;

interface PersonalizedReplacer extends ReplacerWithHelpText
{
    public function replace(string $text, Send $pendingSend): string;
}
