<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\ReplacerWithHelpText;

class ReplacerHelpTextsComponent extends Component
{
    public function replacerHelpTexts(): array
    {
        return collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => app($className))
            ->flatMap(fn (ReplacerWithHelpText $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.replacerHelpTexts');
    }
}
