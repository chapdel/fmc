<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Shared\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeHtmlAction
{
    public function __construct(
        protected PersonalizeTextAction $personalizeTextAction
    ) {
    }

    public function execute(?string $html, Send $pendingSend): string
    {
        return $this->personalizeTextAction->execute($html, $pendingSend);
    }
}
