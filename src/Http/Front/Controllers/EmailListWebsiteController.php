<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailListWebsiteController
{
    use UsesMailcoachModels;

    public function index(EmailList $emailList)
    {
        $campaigns = $this->getCampaignClass()::query()
            ->where('email_list_id', $emailList->id)
            ->orderByDesc('sent_at')
            ->sent()
            ->paginate();

        return view('mailcoach::emailListWebsite.index', [
            'campaigns' => $campaigns,
            'emailList' => $emailList,
        ]);
    }

    public function show(EmailList $emailList, Campaign $campaign)
    {
        if ($campaign->email_list_id !== $emailList->id) {
            abort(404);
        }

        if (! $campaign->isSendingOrSent()) {
            abort(404);
        }

        return view('mailcoach::emailListWebsite.show', [
            'emailList' => $emailList,
            'campaign' => $campaign,
            'webview' => view('mailcoach::campaign.webview', compact('campaign'))->render(),
        ]);
    }
}
