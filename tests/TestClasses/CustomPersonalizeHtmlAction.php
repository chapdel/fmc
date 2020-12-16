<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;

class CustomPersonalizeHtmlAction extends PersonalizeHtmlAction
{
    public function execute($html, Send $pendingSend): string
    {
        $pendingSend->subscriber->update([
            'email' => 'overridden@example.com',
        ]);

        return parent::execute($html, $pendingSend);
    }
}
