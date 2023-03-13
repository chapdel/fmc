<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\TagTypeFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListTagsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(EmailList $emailList, ?Request $request = null)
    {
        parent::__construct(self::getTagClass()::query(), $request);

        $this

            ->where('email_list_id', $emailList->id)
            ->defaultSort('name')
            ->allowedSorts('name', 'updated_at', 'subscriber_count', 'visible_in_preferences')
            ->allowedIncludes(['emailList'])
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter(
                    'name'
                )),
                AllowedFilter::custom('type', new TagTypeFilter()),
            );
    }
}
