<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Exception;
use Spatie\Mailcoach\Actions\Campaigns\PersonalizeHtmlAction;
use Spatie\Mailcoach\Models\Send;

class FailingPersonalizeHtmlForJohnAction extends PersonalizeHtmlAction
{
    public function execute($html, Send $pendingSend): string
    {
        if ($pendingSend->subscriber->email === 'john@example.com') {
            throw new Exception('Could not personalize html');
        }

        return parent::execute($html, $pendingSend);
    }
}
