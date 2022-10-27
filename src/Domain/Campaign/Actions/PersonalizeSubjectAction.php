<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeSubjectAction
{
    public function __construct(
        protected PersonalizeTextAction $personalizeTextAction
    ) {}

    public function execute(string $subject, Send $pendingSend): string
    {
        return $this->personalizeTextAction->execute($subject, $pendingSend);
    }
}
