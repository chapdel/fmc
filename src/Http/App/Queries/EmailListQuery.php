<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        $query = $this->getEmailListClass()::query()
            ->addSelect([
                'active_subscribers_count' => $this->getSubscriberClass()::query()
                    ->selectRaw('count(id)')
                    ->subscribed()
                    ->whereColumn('mailcoach_subscribers.email_list_id', 'mailcoach_email_lists.id'),
            ]);

        parent::__construct($query);

        $this
            ->defaultSort('name')
            ->allowedSorts('name', 'created_at', 'active_subscribers_count')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name'))
            );
    }
}
