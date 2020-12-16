<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Actions\Campaigns\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Models\Campaign;

class CustomPrepareEmailHtmlAction extends PrepareEmailHtmlAction
{
    public function execute(Campaign $campaign): void
    {
        $campaign->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($campaign);
    }
}
