<?php

namespace Spatie\Mailcoach\Http\App\ViewModels\BladeX;

use Spatie\BladeX\ViewModel;
use Spatie\Mailcoach\Support\Replacers\ReplacerWithHelpText;

class ReplacerHelpTextsViewModel extends ViewModel
{
    public function replacerHelpTexts(): array
    {
        return collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->flatMap(fn (ReplacerWithHelpText $replacer) => $replacer->helpText())
            ->toArray();
    }
}
