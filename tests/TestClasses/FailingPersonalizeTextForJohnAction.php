<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Exception;
use Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class FailingPersonalizeTextForJohnAction extends PersonalizeTextAction
{
    public function execute(?string $text, Send $pendingSend): string
    {
        if ($pendingSend->subscriber->email === 'john@example.com') {
            throw new Exception('Could not personalize html');
        }

        return parent::execute($text, $pendingSend);
    }
}
