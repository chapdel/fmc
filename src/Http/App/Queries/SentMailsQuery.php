<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Models\Send;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SentMailsQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(Send::query());

        $this
            ->whereNotNull('sent_at')
            ->defaultSort('-sent_at')
            ->with(['campaign', 'subscription.subscriber'])
            ->allowedSorts(
                'created_at',
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('campaign.name', 'subscription.subscriber.email'))
            );
    }
}
