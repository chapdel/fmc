<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

use Spatie\Mailcoach\Http\App\Controllers\Campaigns\CampaignsIndexController;

class HomeController
{
    public function __invoke()
    {
        return redirect()->route('mailcoach.campaigns');
    }
}
