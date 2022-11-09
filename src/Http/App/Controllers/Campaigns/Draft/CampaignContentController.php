<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\MainNavigation;

class CampaignContentController
{
    use AuthorizesRequests;

    public function edit(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $templateOptions = Template::all()
            ->pluck('name', 'id')
            ->toArray();

        $viewName = $campaign->isEditable()
            ? 'content'
            : 'contentReadOnly';

        app(MainNavigation::class)->activeSection()->add($campaign->name, route('mailcoach.campaigns.content', $campaign));

        return view("mailcoach::app.campaigns.{$viewName}", compact('campaign', 'templateOptions'));
    }
}
