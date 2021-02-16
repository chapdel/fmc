<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\CampaignStatusFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Sorts\CampaignSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AutomatedMailQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct()
    {
        parent::__construct($this::getAutomationMailClass()::query());

        $this
            ->defaultSort('name')
            ->allowedSorts(
                'name',
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name')),
            );
    }
}
