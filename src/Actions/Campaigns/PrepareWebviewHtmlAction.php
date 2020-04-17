<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;

class PrepareWebviewHtmlAction
{
    public function execute(CampaignConcern $campaign)
    {
        $campaign->webview_html = $campaign->htmlWithInlinedCss();

        $campaign->save();
    }
}
