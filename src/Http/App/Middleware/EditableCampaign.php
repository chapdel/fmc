<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;

class EditableCampaign
{
    public function handle(Request $request, $next)
    {
        /** @var \Spatie\Mailcoach\Models\Campaign|null $campaign */
        if (! $campaign = $request->route()->parameter('campaign')) {
            return $next($request);
        }

        return $campaign->isSending() || $campaign->isSent()
            ? redirect()->route('mailcoach.campaigns.summary', $campaign)
            : $next($request);
    }
}
