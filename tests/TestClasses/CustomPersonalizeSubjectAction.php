<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;

class CustomPersonalizeSubjectAction extends PersonalizeSubjectAction
{
    public function execute($subject, Send $pendingSend): string
    {
        $pendingSend->subscriber->update([
            'email' => 'overridden@example.com',
        ]);

        return parent::execute($subject, $pendingSend);
    }
}
