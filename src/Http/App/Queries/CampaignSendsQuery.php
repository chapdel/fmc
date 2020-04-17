<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SendTypeFilter;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\Concerns\Campaign as CampaignConcern;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignSendsQuery extends QueryBuilder
{
    public function __construct(CampaignConcern $campaign)
    {
        parent::__construct(Send::query());

        $this
            ->addSelect(['subscriber_email' => Subscriber::select('email')
                ->whereColumn('subscriber_id', 'mailcoach_subscribers.id')
                ->limit(1),
            ])
            ->with('feedback')
            ->where('campaign_id', $campaign->id)
            ->defaultSort('created_at')
            ->with(['campaign', 'subscriber'])
            ->allowedSorts(
                'sent_at',
                'subscriber_email',
            )
            ->allowedFilters(
                AllowedFilter::custom('type', new SendTypeFilter()),
                AllowedFilter::custom('search', new FuzzyFilter('subscriber.email'))
            );
    }
}
