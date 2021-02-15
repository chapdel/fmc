<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignSendQuery extends QueryBuilder
{
    public function __construct(Subscriber $subscriber)
    {
        $query = Send::query()->where('subscriber_id', $subscriber->id);

        parent::__construct($query);

        $this
            ->defaultSort('-sent_at')
            ->allowedSorts('sent_at')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('campaign.name'))
            );
    }
}
