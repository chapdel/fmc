<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Actions\Campaigns\PrepareSubjectAction;
use Spatie\Mailcoach\Models\Campaign;

class CustomPrepareSubjectAction extends PrepareSubjectAction
{
    public function execute(Campaign $campaign): void
    {
        $campaign->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($campaign);
    }
}
