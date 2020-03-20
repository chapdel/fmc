<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class BooleanFilter implements Filter
{
    protected string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (! is_null($value)) {
            if ($value) {
                $query->whereNotNull($this->field);
            } else {
                $query->whereNull($this->field);
            }
        }

        return $query;
    }
}
