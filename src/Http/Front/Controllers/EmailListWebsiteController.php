<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailListWebsiteController
{
    use UsesMailcoachModels;

    public function index(string $emailListWebsiteSlug = '/')
    {
        $emailList = $this->getEmailList($emailListWebsiteSlug);

        /**
         * If the email list website is on the root of the domain
         * visiting a campaign will result in this route being
         * called instead of the show route, call it here.
         */
        if (! $emailList) {
            return $this->show('/', $emailListWebsiteSlug);
        }

        $campaigns = self::getCampaignClass()::query()
            ->where('email_list_id', $emailList->id)
            ->orderByDesc('sent_at')
            ->sent()
            ->simplePaginate(15);

        return view('mailcoach::emailListWebsite.index', [
            'campaigns' => $campaigns,
            'emailList' => $emailList,
        ]);
    }

    public function show(string $emailListWebsiteSlug, string $campaignUuid)
    {
        $emailList = $this->getEmailList($emailListWebsiteSlug);

        /**
         * If there is no email list website at the root domain
         * we'll redirect to the Mailcoach dashboard to
         * preserve the old functionality.
         */
        if ($emailListWebsiteSlug === '/' && ! $emailList) {
            return redirect()->route('mailcoach.dashboard');
        }

        /** @var $campaign Campaign */
        if (! $campaign = static::getCampaignClass()::findByUuid($campaignUuid)) {
            abort(404);
        }

        abort_unless($emailList->has_website, 404);
        abort_unless($campaign->isSendingOrSent(), 404);

        return view('mailcoach::emailListWebsite.show', [
            'emailList' => $emailList,
            'campaign' => $campaign,
            'webview' => view('mailcoach::campaign.webview', compact('campaign'))->render(),
        ]);
    }

    protected function getEmailList(string $emailListWebsiteSlug): ?EmailList
    {
        return self::getEmailListClass()::query()
            ->where('has_website', true)
            ->where('website_slug', $emailListWebsiteSlug)
            ->first();
    }
}
