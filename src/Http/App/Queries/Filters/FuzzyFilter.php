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

        $query->where(function (Builder $query) use ($values) {
            $this
                ->addDirectFields($query, $values)
                ->addRelationShipFields($query, $values);
        });

        return $query;
    }

    public function addDirectFields(Builder $query, $values): FuzzyFilter
    {
        collect($this->fields)
            ->reject(fn (string $field) => Str::contains($field, '.'))
            ->each(function (string $field) use ($query, $values) {
                foreach ($values as $value) {
                    $value = str_replace('+', ' ', $value);

                    if ($field === 'email_idx_1' && config('mailcoach.encryption.enabled')) {
                        $firstPart = self::getSubscriberClass()::getEncryptedRow()->getBlindIndex('email_first_part', ['email' => $value]);
                        $query->orWhere($field, '=', $firstPart);
                        continue;
                    }

                    if ($field === 'email_idx_2' && config('mailcoach.encryption.enabled')) {
                        $secondPart = self::getSubscriberClass()::getEncryptedRow()->getBlindIndex('email_second_part', ['email' => $value]);
                        $query->orWhere($field, '=', $secondPart);
                        continue;
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
