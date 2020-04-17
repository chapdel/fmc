<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Spatie\Mailcoach\Models\Campaign;

class PrepareWebviewHtmlAction
{
    public function execute(Campaign $campaign)
    {
        $campaign->webview_html = $campaign->htmlWithInlinedCss();

        $campaign->save();
    }
}
