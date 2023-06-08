<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EditableCampaign
{
    use UsesMailcoachModels;

    public function handle(Request $request, $next)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign|null $campaign */
        $campaign = $request->route()->parameter('campaign');

        if (! $campaign) {
            return $next($request);
        }

        if (is_string($campaign)) {
            $campaign = self::getCampaignClass()::find($campaign);
        }

        return $campaign?->isEditable()
            ? $next($request)
            : redirect()->route('mailcoach.campaigns.summary', $campaign);
    }
}
