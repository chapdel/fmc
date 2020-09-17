<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\CampaignOpen;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignOpensQuery extends QueryBuilder
{
    public int $totalCount;

    public function __construct(Campaign $campaign)
    {
        $prefix = DB::getTablePrefix();

        $query = CampaignOpen::query()
            ->selectRaw("
                {$prefix}mailcoach_campaign_opens.subscriber_id as subscriber_id,
                {$prefix}mailcoach_subscribers.email as subscriber_email,
                count({$prefix}mailcoach_campaign_opens.subscriber_id) as open_count,
                min({$prefix}mailcoach_campaign_opens.created_at) AS first_opened_at
            ")
            ->join('mailcoach_campaigns', 'mailcoach_campaigns.id', '=', 'mailcoach_campaign_opens.campaign_id')
            ->join('mailcoach_subscribers', 'mailcoach_subscribers.id', '=', 'mailcoach_campaign_opens.subscriber_id')
            ->where('mailcoach_campaigns.id', $campaign->id);

        $this->totalCount = $query->count();

        parent::__construct($query);

        $this
            ->defaultSort('-first_opened_at')
            ->allowedSorts('email', 'open_count', 'first_opened_at')
            ->groupBy('mailcoach_campaign_opens.subscriber_id', 'mailcoach_subscribers.email')
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'email'
                    )
                )
            );
    }
}
