<?php

namespace Spatie\Mailcoach\Http\Api\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignLinksQuery extends QueryBuilder
{
    public function __construct(Campaign $campaign, ?Request $request = null)
    {
        $query = $campaign
            ->contentItem
            ->links()
            ->getQuery();

        parent::__construct($query, $request);

        $this
            ->defaultSort('-unique_click_count')
            ->allowedSorts('unique_click_count', 'click_count')
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'url'
                    )
                )
            );
    }
}
