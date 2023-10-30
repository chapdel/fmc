<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class CustomPersonalizeTextAction extends PersonalizeTextAction
{
    public function execute($html, Send $pendingSend): string
    {
        $pendingSend->subscriber->update([
            'email' => 'overridden@example.com',
        ]);

        return parent::execute($html, $pendingSend);
    }
}
