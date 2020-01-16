<?php

namespace Spatie\Mailcoach\Support\Replacers;

use Spatie\Mailcoach\Models\Send;

interface PersonalizedReplacer extends ReplacerWithHelpText
{
    public function replace(string $html, Send $pendingSend): string;
}
