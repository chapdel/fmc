<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class EmailListWebsiteController
{
    use UsesMailcoachModels;

    public function index(string $emailListWebsiteSlug)
    {
        $emailList = $this->getEmailList($emailListWebsiteSlug);

        $campaigns = $this->getCampaignClass()::query()
            ->where('email_list_id', $emailList->id)
            ->orderByDesc('sent_at')
            ->sent()
            ->simplePaginate(5);

        return view('mailcoach::emailListWebsite.index', [
            'campaigns' => $campaigns,
            'emailList' => $emailList,
        ]);
    }

    public function show(string $emailListWebsiteSlug, string $campaignUuid)
    {
        $emailList = $this->getEmailList($emailListWebsiteSlug);

        /** @var $campaign Campaign */
        if (! $campaign = static::getCampaignClass()::findByUuid($campaignUuid)) {
            abort(404);
        }

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

    protected function getEmailList(string $emailListWebsiteSlug): EmailList
    {
        return $this->getEmailListClass()::query()
            ->where('has_website', true)
            ->where('website_slug', $emailListWebsiteSlug)
            ->firstOrFail();
    }
}
