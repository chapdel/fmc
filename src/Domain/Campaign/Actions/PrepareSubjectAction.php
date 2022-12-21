<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

/**
 * @deprecated
 */
class PrepareSubjectAction
{
    public function execute(Campaign $campaign): void
    {
        // Deprecated. By default we don't do anything here anymore
    }
}
