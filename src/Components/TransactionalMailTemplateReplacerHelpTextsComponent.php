<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\ReplacerWithHelpText;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class TransactionalMailTemplateReplacerHelpTextsComponent extends Component
{
    public function __construct(
        public TransactionalMailTemplate $template
    ) {
    }

    public function replacerHelpTexts(): array
    {
        return collect($this->template->replacers)
            ->filter(fn (string $className) => class_exists($className))
            ->map(fn (string $className) => app($className))
            ->flatMap(fn (ReplacerWithHelpText $replacer) => $replacer->helpText())
            ->toArray();
    }

    public function render()
    {
        return view('mailcoach::app.components.replacerHelpTexts');
    }
}
