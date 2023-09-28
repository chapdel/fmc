<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Jobs\ExportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\Api\Queries\EmailListSubscribersQuery;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\MainNavigation;
use Spatie\QueryBuilder\QueryBuilder;

class SubscribersComponent extends TableComponent
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

    public function getTableQuery(): Builder
    {
        return self::getSubscriberClass()::query()
            ->where(self::getSubscriberTableName().'.email_list_id', $this->emailList->id)
            ->with('emailList', 'tags');
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate($this->getTableRecordsPerPage())->onEachSide(1);
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->label('')
                ->icon(fn (Subscriber $record) => match (true) {
                    $record->isUnconfirmed() => 'heroicon-o-question-mark-circle',
                    $record->isSubscribed() => 'heroicon-o-check-circle',
                    $record->isUnsubscribed() => 'heroicon-o-x-circle',
                    default => '',
                })
                ->color(fn (Subscriber $record) => match (true) {
                    $record->isUnconfirmed() => 'warning',
                    $record->isSubscribed() => 'success',
                    $record->isUnsubscribed() => 'danger',
                    default => '',
                })
                ->tooltip(fn (Subscriber $record) => match (true) {
                    $record->isUnconfirmed() => __mc('Unconfirmed'),
                    $record->isSubscribed() => __mc('Subscribed'),
                    $record->isUnsubscribed() => __mc('Unsubscribed'),
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
                ->getStateUsing(fn (Subscriber $record) => match (true) {
                    $record->isUnsubscribed() => $record->unsubscribed_at?->toMailcoachFormat(),
                    $record->isUnconfirmed() => $record->created_at->toMailcoachFormat(),
                    default => $record->subscribed_at?->toMailcoachFormat(),
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
                    ->action(fn (Subscriber $record) => $this->resendConfirmation($record))
                    ->hidden(fn (Subscriber $record) => ! $record->isUnconfirmed()),
                Action::make('confirm')
                    ->label(__mc('Confirm'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $record) => $this->confirm($record))
                    ->hidden(fn (Subscriber $record) => ! $record->isUnconfirmed()),
                Action::make('unsubscribe')
                    ->label(__mc('Unsubscribe'))
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $record) => $this->unsubscribe($record))
                    ->hidden(fn (Subscriber $record) => ! $record->isSubscribed()),
                Action::make('resubscribe')
                    ->label(__mc('Resubscribe'))
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $record) => $this->resubscribe($record))
                    ->hidden(fn (Subscriber $record) => $record->isSubscribed()),
                Action::make('delete')
                    ->label(__mc('Delete'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(fn (Subscriber $record) => $this->deleteSubscriber($record)),
            ]),
        ];
    }

    protected function applyGlobalSearchToTableQuery(
        \Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $search = trim(strtolower($this->getTableSearch()));

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
        /**
         * @TODO: Remove this when https://github.com/filamentphp/filament/issues/7634 is fixed
         * Currently tests fail when using checkbox components from Filament.
         */
        if (app()->environment('testing')) {
            return [];
        }

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
                    default => $query,
                })
                ->columnspan(2),
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
            SelectFilter::make('opened_campaign')
                ->label(__mc('Opened campaign'))
                ->multiple()
                ->placeholder('')
                ->options(fn () => $this->emailList->campaigns()->where('status', CampaignStatus::Sent)->pluck('name', 'uuid'))
                ->query(function (Builder $query, array $data) {
                    if (! $data['values']) {
                        return $query;
                    }

                    return $query->whereHas('opens', function (Builder $query) use ($data) {
                        $query->whereIn('campaign_id',
                            self::getCampaignClass()::whereIn('uuid', $data['values'])->select('id'));
                    });
                }),
            SelectFilter::make('opened_automation_mail')
                ->label(__mc('Opened automation mail'))
                ->multiple()
                ->placeholder('')
                ->options(fn () => self::getAutomationMailClass()::query()->pluck('name', 'uuid'))
                ->query(function (Builder $query, array $data) {
                    if (! $data['values']) {
                        return $query;
                    }

                    return $query->whereHas('automationMailOpens', function (Builder $query) use ($data) {
                        $query->whereIn('automation_mail_id',
                            self::getAutomationMailClass()::whereIn('uuid', $data['values'])->select('id'));
                    });
                }),
            SelectFilter::make('clicked_campaign')
                ->label(__mc('Clicked campaign'))
                ->multiple()
                ->placeholder('')
                ->options(fn () => $this->emailList->campaigns()->where('status', CampaignStatus::Sent)->pluck('name', 'uuid'))
                ->query(function (Builder $query, array $data) {
                    if (! $data['values']) {
                        return $query;
                    }

                    return $query->whereHas('clicks', function (Builder $query) use ($data) {
                        $query->whereHas('link', function (Builder $query) use ($data) {
                            $query->whereIn('campaign_id', self::getCampaignClass()::whereIn('uuid', $data['values'])->select('id'));
                        });
                    });
                }),
            SelectFilter::make('clicked_automation_mail')
                ->label(__mc('Clicked automation mail'))
                ->multiple()
                ->placeholder('')
                ->options(fn () => self::getAutomationMailClass()::query()->pluck('name', 'uuid'))
                ->query(function (Builder $query, array $data) {
                    if (! $data['values']) {
                        return $query;
                    }

                    return $query->whereHas('automationMailClicks', function (Builder $query) use ($data) {
                        $query->whereHas('link', function (Builder $query) use ($data) {
                            $query->whereIn('automation_mail_id', self::getAutomationMailClass()::whereIn('uuid', $data['values'])->select('id'));
                        });
                    });
                }),
            Filter::make('opens')
                ->label(__mc('Has opened any email'))
                ->toggle()
                ->query(fn (Builder $query) => $query->where(function (Builder $query) {
                    $query->whereHas('opens')
                        ->orWhereHas('automationMailOpens');
                })),
            Filter::make('clicks')
                ->label(__mc('Has clicked any email'))
                ->toggle()
                ->query(fn (Builder $query) => $query->where(function (Builder $query) {
                    $query->whereHas('clicks')
                        ->orWhereHas('automationMailClicks');
                })),
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
        return function (Subscriber $record) {
            return route('mailcoach.emailLists.subscriber.details', [$this->emailList, $record]);
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

                    notify(__mc('Added tags to :count subscribers', ['count' => $subscribers->count()]));
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

                    notify(__mc('Removed tags from :count subscribers', ['count' => $subscribers->count()]));
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

                    return $this->export(
                        header: array_unique(array_keys($header)),
                        rows: $subscribers,
                        formatRow: function (Subscriber $subscriber) use ($header) {
                            return array_merge($header, $subscriber->toExportRow());
                        },
                        title: "{$this->emailList->name} subscribers",
                    );
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

                    notify(__mc("Successfully unsubscribed {$count} subscribers."));
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

                    notify(__mc('Deleted :count subscribers', ['count' => $count]));
                }),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('export_subscribers')
                ->label(function () {
                    return __mc('Export :count subscribers', ['count' => Str::shortNumber($this->getAllTableRecordsCount())]);
                })
                ->requiresConfirmation()
                ->color('gray')
                ->icon('heroicon-o-cloud-arrow-down')
                ->action(function () {
                    $export = self::getSubscriberExportClass()::create([
                        'email_list_id' => $this->emailList->id,
                        'filters' => $this->tableFilters,
                    ]);

                    dispatch(new ExportSubscribersJob(
                        subscriberExport: $export,
                        user: Auth::user(),
                    ));

                    notify(__mc('Subscriber export successfully queued.'));

                    return redirect()->route('mailcoach.emailLists.subscriber-exports', [$this->emailList]);
                }),
        ];
    }

    public function subscribersCount(): int
    {
        return once(function () {
            return $this->emailList->allSubscribers()->count();
        });
    }

    public function getTable(): Table
    {
        $table = parent::getTable();

        if ($this->subscribersCount() >= 10_000) {
            $table->selectCurrentPageOnly();
        }

        $table->filtersFormColumns(2);

        return $table;
    }

    public function deleteSubscriber(Subscriber $subscriber)
    {
        $this->authorize('delete', $subscriber);

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Mailcoach::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($subscriber);

        notify(__mc('Subscriber :subscriber was deleted.', ['subscriber' => $subscriber->email]));
    }

    public function resubscribe(Subscriber $subscriber)
    {
        if (! $subscriber->isUnsubscribed()) {
            notify(__mc('Can only resubscribe unsubscribed subscribers'), 'error');

            return;
        }

        $subscriber->resubscribe();

        notify(__mc(':subscriber has been resubscribed.', ['subscriber' => $subscriber->email]));
    }

    public function unsubscribe(Subscriber $subscriber)
    {
        if (! $subscriber->isSubscribed()) {
            notify(__mc('Can only unsubscribe a subscribed subscriber'), 'error');

            return;
        }

        $subscriber->unsubscribe();

        notify(__mc(':subscriber has been unsubscribed.', ['subscriber' => $subscriber->email]));
    }

    public function confirm(Subscriber $subscriber)
    {
        if ($subscriber->status !== SubscriptionStatus::Unconfirmed) {
            notify(__mc('Can only subscribe unconfirmed emails'), 'error');

            return;
        }

        $subscriber->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        notify(__mc(':subscriber has been confirmed.', ['subscriber' => $subscriber->email]));
    }

    public function resendConfirmation(Subscriber $subscriber)
    {
        resolve(SendConfirmSubscriberMailAction::class)->execute($subscriber);

        notify(__mc('A confirmation mail has been sent to :subscriber', ['subscriber' => $subscriber->email]));
    }

    public function deleteUnsubscribes()
    {
        $this->authorize('update', $this->emailList);

        $this->emailList->allSubscribers()->unsubscribed()->delete();

        notify(__mc('All unsubscribers of the list have been deleted.'));
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
