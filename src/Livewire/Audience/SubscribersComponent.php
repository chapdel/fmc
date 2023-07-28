<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Livewire\FilamentDataTableComponent;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SubscribersComponent extends FilamentDataTableComponent
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name.' ', route('mailcoach.emailLists'));
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {
        return self::getSubscriberClass()::query()
            ->where(self::getSubscriberTableName().'.email_list_id', $this->emailList->id)
            ->with('emailList', 'tags');
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->label('')
                ->icon(fn (Subscriber $subscriber) => match (true) {
                    $subscriber->isUnconfirmed() => 'heroicon-o-question-mark-circle',
                    $subscriber->isSubscribed() => 'heroicon-o-check-circle',
                    $subscriber->isUnsubscribed() => 'heroicon-o-x-circle',
                    default => '',
                })
                ->color(fn (Subscriber $subscriber) => match (true) {
                    $subscriber->isUnconfirmed() => 'warning',
                    $subscriber->isSubscribed() => 'success',
                    $subscriber->isUnsubscribed() => 'danger',
                    default => '',
                })
                ->tooltip(fn (Subscriber $subscriber) => match (true) {
                    $subscriber->isUnconfirmed() => __mc('Unconfirmed'),
                    $subscriber->isSubscribed() => __mc('Subscribed'),
                    $subscriber->isUnsubscribed() => __mc('Unsubscribed'),
                    default => '',
                }),
            TextColumn::make('email')
                ->label(__mc('Email'))
                ->searchable()
                ->sortable(),
            TextColumn::make('tags.name')
                ->view('mailcoach::app.emailLists.subscribers.columns.tags')
                ->searchable(),
            TextColumn::make('date')
                ->label(__mc('Date'))
                ->sortable(query: function (Builder $query) {
                    $query->orderBy(DB::raw('unsubscribed_at, subscribed_at, created_at'), $this->tableSortDirection);
                })
                ->getStateUsing(fn (Subscriber $subscriber) => match (true) {
                    $subscriber->isUnsubscribed() => $subscriber->unsubscribed_at?->toMailcoachFormat(),
                    $subscriber->isUnconfirmed() => $subscriber->created_at->toMailcoachFormat(),
                    default => $subscriber->subscribed_at?->toMailcoachFormat(),
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('resend_confirmation')
                    ->label(__mc('Resend confirmation mail'))
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $subscriber) => $this->resendConfirmation($subscriber))
                    ->hidden(fn (Subscriber $subscriber) => ! $subscriber->isUnconfirmed()),
                Action::make('confirm')
                    ->label(__mc('Confirm'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $subscriber) => $this->confirm($subscriber))
                    ->hidden(fn (Subscriber $subscriber) => ! $subscriber->isUnconfirmed()),
                Action::make('unsubscribe')
                    ->label(__mc('Unsubscribe'))
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $subscriber) => $this->unsubscribe($subscriber))
                    ->hidden(fn (Subscriber $subscriber) => ! $subscriber->isSubscribed()),
                Action::make('resubscribe')
                    ->label(__mc('Resubscribe'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $subscriber) => $this->resubscribe($subscriber))
                    ->hidden(fn (Subscriber $subscriber) => $subscriber->isSubscribed()),
                Action::make('delete')
                    ->label(__mc('Delete'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $subscriber) => $this->deleteSubscriber($subscriber)),
            ]),
        ];
    }

    protected function applyGlobalSearchToTableQuery(
        \Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $search = trim(strtolower($this->getTableSearch()));

        if (config('mailcoach.encryption.enabled')) {
            if (str_contains($search, '@')) {
                $query->orWhere(function (\Illuminate\Database\Eloquent\Builder $builder) use ($search, $query) {
                    $builder->whereBlind('email', 'email_first_part', $search);
                    $builder->whereBlind('email', 'email_second_part', $search);

                    $query->getQuery()->joins = $builder->getQuery()->joins;
                });
            } else {
                $query->orWhereBlind('email', 'email_first_part', Str::finish($search, '@'));
                $query->orWhereBlind('email', 'email_second_part', Str::start($search, '@'));
            }

            $query->orWhereBlind('first_name', 'first_name', $search);
            $query->orWhereBlind('last_name', 'last_name', $search);

            return parent::applyGlobalSearchToTableQuery($query);
        }

        if (str_contains($search, '@')) {
            $clone = clone $query;

            if ($clone->where('email', $search)->count() > 0) {
                return $query->where('email', $search);
            }
        }

        return parent::applyGlobalSearchToTableQuery($query);
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label(__mc('Status'))
                ->options([
                    'subscribed' => __mc('Subscribed'),
                    'unsubscribed' => __mc('Unsubscribed'),
                    'unconfirmed' => __mc('Unconfirmed'),
                ])
                ->query(fn (Builder $query, array $data) => match ($data['value']) {
                    'subscribed' => $query->subscribed(),
                    'unsubscribed' => $query->unsubscribed(),
                    'unconfirmed' => $query->unconfirmed(),
                    null => $query,
                }),
            SelectFilter::make('tags')
                ->label(__mc('Tags'))
                ->multiple()
                ->options(fn () => $this->emailList->tags()->where('type', TagType::Default)->pluck('name', 'uuid'))
                ->query(fn (Builder $query, array $data) => $this->applyTagsQuery($query, $data['values'])),
            SelectFilter::make('mailcoach_tags')
                ->label(__mc('Mailcoach tags'))
                ->multiple()
                ->options(fn () => $this->emailList->tags()->where('type', TagType::Mailcoach)->pluck('name', 'uuid'))
                ->query(fn (Builder $query, array $data) => $this->applyTagsQuery($query, $data['values'])),
        ];
    }

    protected function applyTagsQuery(Builder $query, array $values): Builder
    {
        if (! $values) {
            return $query;
        }

        $tagIds = self::getTagClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->where(fn (Builder $query) => $query->whereIn('uuid', $values))
            ->pluck('id');

        if (! $tagIds->count()) {
            return $query;
        }

        return $query->where(
            DB::table('mailcoach_email_list_subscriber_tags')
                ->selectRaw('count(*)')
                ->where(self::getSubscriberTableName().'.id', DB::raw('mailcoach_email_list_subscriber_tags.subscriber_id'))
                ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $tagIds->toArray()),
            '>=', $tagIds->count()
        );
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Subscriber $subscriber) {
            return route('mailcoach.emailLists.subscriber.details', [$this->emailList, $subscriber]);
        };
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('Add tags')
                ->label(__mc('Add tags'))
                ->icon('heroicon-o-plus-circle')
                ->action(function (Collection $subscribers, array $data) {
                    $tags = self::getTagClass()::whereIn('uuid', $data['tags'])->pluck('name')->toArray();

                    $subscribers->each(function (Subscriber $subscriber) use ($tags) {
                        $subscriber->addTags($tags);
                    });

                    $this->flash(__mc('Added tags to :count subscribers', ['count' => $subscribers->count()]));
                })
                ->form([
                    Select::make('tags')
                        ->label(__mc('Tags'))
                        ->multiple()
                        ->options(self::getTagClass()::where('type', TagType::Default)->orderBy('name')->pluck('name', 'uuid'))
                        ->required(),
                ]),
            BulkAction::make('Remove tags')
                ->label(__mc('Remove tags'))
                ->icon('heroicon-o-minus-circle')
                ->action(function (Collection $subscribers, array $data) {
                    $tags = self::getTagClass()::whereIn('uuid', $data['tags'])->pluck('name')->toArray();

                    $subscribers->each(function (Subscriber $subscriber) use ($tags) {
                        $subscriber->removeTags($tags);
                    });

                    $this->flash(__mc('Removed tags from :count subscribers', ['count' => $subscribers->count()]));
                })
                ->form([
                    Select::make('tags')
                        ->label(__mc('Tags'))
                        ->multiple()
                        ->options(self::getTagClass()::where('type', TagType::Default)->orderBy('name')->pluck('name', 'uuid'))
                        ->required(),
                ]),
            BulkAction::make('export')
                ->label(__mc('Export selected'))
                ->icon('heroicon-o-cloud-arrow-down')
                ->action(function (Collection $subscribers) {
                    ini_set('max_execution_time', '0');

                    return response()->streamDownload(function () use ($subscribers) {
                        $subscriberCsv = SimpleExcelWriter::streamDownload("{$this->emailList->name} subscribers.csv");

                        $header = [
                            'email' => null,
                            'first_name' => null,
                            'last_name' => null,
                            'tags' => null,
                            'subscribed_at' => null,
                            'unsubscribed_at' => null,
                        ];

                        $subscribers->each(function (Subscriber $subscriber) use (&$header) {
                            $attributes = array_keys($subscriber->extra_attributes->toArray());
                            $attributes = collect($attributes)->mapWithKeys(fn ($key) => [$key => null])->toArray();
                            ksort($attributes);

                            $header = array_merge($header, $attributes);
                        });

                        $subscriberCsv->addHeader(array_unique(array_keys($header)));

                        $subscribers->each(function (Subscriber $subscriber) use ($subscriberCsv, $header) {
                            $subscriberCsv->addRow(array_merge($header, $subscriber->toExportRow()));

                            flush();
                        });

                        $subscriberCsv->close();
                    }, "{$this->emailList->name} subscribers.csv", [
                        'Content-Type' => 'text/csv',
                    ]);
                }),
            BulkAction::make('Unsubscribe')
                ->label(__mc('Unsubscribe'))
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
                ->action(function (Collection $subscribers) {
                    $count = $subscribers->count();

                    $subscribers->each(function (Subscriber $subscriber) {
                        $subscriber->unsubscribe();
                    });

                    $this->flash(__mc("Successfully unsubscribed {$count} subscribers."));
                }),
            BulkAction::make('Delete')
                ->label(__mc('Delete'))
                ->requiresConfirmation()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->deselectRecordsAfterCompletion()
                ->action(function (Collection $records) {
                    $count = $records->count();

                    $records->each(function (Subscriber $subscriber) {
                        /** @var DeleteSubscriberAction $deleteSubscriberAction */
                        $deleteSubscriberAction = Mailcoach::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);
                        $deleteSubscriberAction->execute($subscriber);
                    });

                    $this->flash(__mc('Deleted :count subscribers', ['count' => $count]));
                }),
        ];
    }

    public function deleteSubscriber(Subscriber $subscriber)
    {
        $this->authorize('delete', $subscriber);

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Mailcoach::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($subscriber);

        $this->flash(__mc('Subscriber :subscriber was deleted.', ['subscriber' => $subscriber->email]));
    }

    public function resubscribe(Subscriber $subscriber)
    {
        if (! $subscriber->isUnsubscribed()) {
            $this->flash(__mc('Can only resubscribe unsubscribed subscribers'), 'error');

            return;
        }

        $subscriber->resubscribe();

        $this->flash(__mc(':subscriber has been resubscribed.', ['subscriber' => $subscriber->email]));
    }

    public function unsubscribe(Subscriber $subscriber)
    {
        if (! $subscriber->isSubscribed()) {
            $this->flash(__mc('Can only unsubscribe a subscribed subscriber'), 'error');

            return;
        }

        $subscriber->unsubscribe();

        $this->flash(__mc(':subscriber has been unsubscribed.', ['subscriber' => $subscriber->email]));
    }

    public function confirm(Subscriber $subscriber)
    {
        if ($subscriber->status !== SubscriptionStatus::Unconfirmed) {
            $this->flash(__mc('Can only subscribe unconfirmed emails'), 'error');

            return;
        }

        $subscriber->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $this->flash(__mc(':subscriber has been confirmed.', ['subscriber' => $subscriber->email]));
    }

    public function resendConfirmation(Subscriber $subscriber)
    {
        resolve(SendConfirmSubscriberMailAction::class)->execute($subscriber);

        $this->flash(__mc('A confirmation mail has been sent to :subscriber', ['subscriber' => $subscriber->email]));
    }

    public function deleteUnsubscribes()
    {
        $this->authorize('update', $this->emailList);

        $this->emailList->allSubscribers()->unsubscribed()->delete();

        $this->flash(__mc('All unsubscribers of the list have been deleted.'));
    }

    public function getTitle(): string
    {
        return __mc('Subscribers');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'createData' => [
                'emailList' => $this->emailList,
            ],
            'create' => Auth::user()->can('create', self::getSubscriberClass())
                ? 'subscriber'
                : null,
        ];
    }

    public function getQuery(Request $request): QueryBuilder
    {
        return new EmailListSubscribersQuery($this->emailList, $request);
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        $results = null;

        if ($search = ($request->get('filter')['search'] ?? null)) {
            /** Try a fast lookup by exact email first */
            $request->filter['search'] = null;
            $request->filter['email'] = $search;
            $exactResult = $this->getQuery($request)->paginate($request->per_page);

            /** Reset the request values */
            $request->filter['search'] = $search;
            $request->filter['email'] = null;

            /** If we have a result, use this instead of doing the expensive search below */
            if ($exactResult->total() > 0) {
                $results = $exactResult;
            }
        }

        $results ??= $this->getQuery($request)->paginate($request->per_page);

        return [
            'subscribers' => $results,
            'emailList' => $this->emailList,
            'allSubscriptionsCount' => $this->emailList->allSubscriptionsCount(),
            'totalSubscriptionsCount' => $this->emailList->totalSubscriptionsCount(),
            'unconfirmedCount' => $this->emailList->unconfirmedCount(),
            'unsubscribedCount' => $this->emailList->unsubscribedCount(),
        ];
    }
}
