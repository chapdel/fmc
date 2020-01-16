<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Feed\Feed;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\EmailList;

class EmailListCampaignsFeedController
{
    public function __invoke(string $emailListUuid)
    {
        if (! $emailList = EmailList::findByUuid($emailListUuid)) {
            abort(404);
        }

        if (! $emailList->campaigns_feed_enabled) {
            abort(404);
        }

        $campaigns = $emailList->campaigns()
            ->where('status', CampaignStatus::SENT)
            ->orderByDesc('sent_at')
            ->take(50)
            ->get();

        return new Feed("{$emailList->name} campaigns", $campaigns);
    }
}
