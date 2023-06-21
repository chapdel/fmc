<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomatedMailQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request = null)
    {
        parent::__construct($this::getAutomationMailClass()::query(), $request);

        $this
            ->defaultSort('name')
            ->allowedSorts(
                'name',
                'sent_to_number_of_subscribers',
                'unique_open_count',
                'unique_click_count',
                'created_at'
            )
            ->allowedFilters(
                AllowedFilter::callback('automation_uuid', function (Builder $query, $value) {
                    $class = self::getAutomationMailClass();
                    $shortname = (new ReflectionClass(new $class))->getShortName();

                    $automationMailIds = self::getAutomationActionClass()::query()
                        ->whereHas('automation', fn (Builder $query) => $query->where('uuid', $value))
                        ->whereRaw('FROM_BASE64(action) like \'%'.$shortname.'%\'')
                        ->get()
                        ->map(function (Action $action) use ($shortname) {
                            /**
                             * We want to get any action that has an automation email
                             * referenced. Therefore, we need to parse serialized
                             * string of the action to get the model identifier.
                             */
                            $rawAction = base64_decode($action->getRawOriginal('action'));
                            $idPart = Str::after($rawAction, $shortname.'";s:2:"id";i:');
                            $id = Str::before($idPart, ';');

                            return (int) $id;
                        });

                    $query->whereIn('id', $automationMailIds);
                }),
                AllowedFilter::custom('search', new FuzzyFilter('name')),
            );
    }
}
