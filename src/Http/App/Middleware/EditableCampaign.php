<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;

class EditableCampaign
{
    public function handle(Request $request, $next)
    {
        /** @var $campaign \Spatie\Mailcoach\Models\Campaign */
        if (! $campaign = $request->route()->parameter('campaign')) {
            return $next($request);
        }

        return $campaign->isSending() || $campaign->isSent()
            ? redirect()->route('mailcoach.campaigns.summary', $campaign)
            : $next($request);
    }
}
