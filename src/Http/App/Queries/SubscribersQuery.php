<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubscribersQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct(
            $this->getSubscriberClass()::query()
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
