<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SendTypeFilter;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Send;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SendsQuery extends QueryBuilder
{
    public function __construct(Campaign $campaign)
    {
        parent::__construct(Send::query());

        $this
            ->with('feedback')
            ->where('campaign_id', $campaign->id)
            ->defaultSort('created_at')
            ->with(['campaign', 'subscriber'])
            ->allowedSorts(
                'sent_at',
            )
            ->allowedFilters(
                AllowedFilter::custom('type', new SendTypeFilter()),
                AllowedFilter::custom('search', new FuzzyFilter('subscriber.email'))
            );
    }
}
