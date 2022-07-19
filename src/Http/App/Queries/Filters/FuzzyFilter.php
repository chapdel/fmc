<?php

namespace Spatie\Mailcoach\Http\App\Queries\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\Filters\Filter;

class FuzzyFilter implements Filter
{
    use UsesMailcoachModels;

    /** @var string[] */
    protected array $fields;

    public function __construct(string ...$fields)
    {
        $this->fields = $fields;
    }

    public function __invoke(Builder $query, $values, string $property): Builder
    {
        $values = Arr::wrap($values);

        $query->where(function (Builder $builder) use ($values, $query) {
            $this
                ->addDirectFields($builder, $values)
                ->addRelationShipFields($builder, $values);

            $query->getQuery()->joins = $builder->getQuery()->joins;
        });

        return $query;
    }

    public function addDirectFields(Builder $query, $values): FuzzyFilter
    {
        collect($this->fields)
            ->reject(fn (string $field) => Str::contains($field, '.'))
            ->each(function (string $field) use ($query, $values) {
                foreach ($values as $value) {
                    $value = trim(str_replace('+', ' ', $value));

                    if ($query->from === self::getSubscriberTableName() && config('mailcoach.encryption.enabled')) {
                        if ($field === 'email') {
                            if (str_contains($value, '@')) {
                                $query->orWhere(function (Builder $builder) use ($value, $query) {
                                    $builder->whereBlind('email', 'email_first_part', $value);
                                    $builder->whereBlind('email', 'email_second_part', $value);

                                    $query->getQuery()->joins = $builder->getQuery()->joins;
                                });

                                continue;
                            }

                            foreach (explode('@', $value) as $emailPart) {
                                $query->orWhereBlind('email', 'email_first_part', Str::finish($emailPart, '@'));
                                $query->orWhereBlind('email', 'email_second_part', Str::start($emailPart, '@'));
                            }

                            continue;
                        }

                        if ($field === 'first_name') {
                            $query->orWhereBlind('first_name', 'first_name', $value);

                            continue;
                        }

                        if ($field === 'last_name') {
                            $query->orWhereBlind('last_name', 'last_name', $value);

                            continue;
                        }
                    }

                    $query->orWhere($field, 'LIKE', "%{$value}%");
                }
            });

        return $this;
    }

    public function addRelationShipFields(Builder $query, $values): FuzzyFilter
    {
        collect($this->fields)
            ->filter(fn (string $field) => Str::contains($field, '.'))
            ->each(function (string $field) use ($query, $values) {
                [$relation, $field] = explode('.', $field);

                foreach ($values as $value) {
                    $query->orWhereHas($relation, function (Builder $query) use ($field, $value) {
                        $value = str_replace('+', ' ', $value);
                        $query->where($field, 'LIKE', "%{$value}%");
                    });
                }
            });

        return $this;
    }
}
