<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Shared\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeHtmlAction
{
    public function __construct(
        protected PersonalizeTextAction $personalizeTextAction
    ) {}

    public function execute($html, Send $pendingSend): string
    {
        return $this->personalizeTextAction->execute($html, $pendingSend);
    }
}
