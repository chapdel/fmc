<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;
use Spatie\Mailcoach\Support\Replacers\ReplacerWithHelpText;

class ReplacerHelpTextsComponent extends Component
{
    public function replacerHelpTexts(): array
    {
        return collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->flatMap(fn (ReplacerWithHelpText $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.replacerHelpTexts');
    }
}
