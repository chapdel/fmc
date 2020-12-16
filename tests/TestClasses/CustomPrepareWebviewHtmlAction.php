<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Actions\Campaigns\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Models\Campaign;

class CustomPrepareWebviewHtmlAction extends PrepareWebviewHtmlAction
{
    public function execute(Campaign $campaign): void
    {
        $campaign->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($campaign);
    }
}
