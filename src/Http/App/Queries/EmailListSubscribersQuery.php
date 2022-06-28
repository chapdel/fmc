<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SubscriberStatusFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListSubscribersQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(EmailList $emailList, ?Request $request = null)
    {
        $subscribersQuery = self::getSubscriberClass()::query()
            ->where('email_list_id', $emailList->id)
            ->with('emailList', 'tags');

        parent::__construct($subscribersQuery, $request);

        $this
            ->allowedSorts('created_at', 'updated_at', 'subscribed_at', 'unsubscribed_at', 'email', 'first_name', 'last_name', 'id')
            ->allowedFilters(
                AllowedFilter::callback('email', function (Builder $query, $value) {
                    if (config('mailcoach.encryption.enabled')) {
                        $firstPart = self::getSubscriberClass()::getEncryptedRow()->getBlindIndex('email_first_part', ['email' => $value]);
                        $secondPart = self::getSubscriberClass()::getEncryptedRow()->getBlindIndex('email_second_part', ['email' => $value]);

                        return $query->where(function (Builder $query) use ($secondPart, $firstPart) {
                            $query->where('email_idx_1', '=', $firstPart)
                                ->orWhere('email_idx_2', '=', $secondPart);
                        });
                    }

                    return $query->where('email', $value);
                }),
                AllowedFilter::custom('search', new FuzzyFilter(
                    'email',
                    'email_idx_1',
                    'email_idx_2',
                    'first_name',
                    'last_name',
                    'tags.name'
                )),
                AllowedFilter::custom('status', new SubscriberStatusFilter())
            );

        $request?->input('filter.status') === SubscriptionStatus::Unsubscribed
            ? $this->defaultSort('-unsubscribed_at')
            : $this->defaultSort('-created_at', '-id');
    }
}
