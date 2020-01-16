<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubscribersQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(
            Subscriber::query()
                ->with('emailList')
        );

        $this
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'email', 'first_name', 'last_name'])
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('email', 'first_name', 'last_name'))
            );
    }
}
