<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Models\Template;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TemplatesQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(Template::query());

        $this
            ->defaultSort('name')
            ->allowedSorts(
                'name',
                'updated_at'
            )
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter('name'))
            );
    }
}
