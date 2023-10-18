<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Livewire\TableComponent;

class ListsComponent extends TableComponent
{
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->sortable()
                ->searchable()
                ->view('mailcoach::app.emailLists.columns.name')
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('active_subscribers_count')
                ->label(__mc('Active'))
                ->sortable()
                ->numeric()
                /** @phpstan-ignore-next-line The query adds this field */
                ->getStateUsing(fn (EmailList $record) => Str::shortNumber($record->active_subscribers_count)),
            TextColumn::make('created_at')
                ->label(__mc('Created'))
                ->sortable()
                ->date(config('mailcoach.date_format')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Delete')
                ->action(function (EmailList $record) {
                    $this->authorize('delete', $record);

                    return $record->delete();
                })
                ->requiresConfirmation()
                ->label(' ')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (EmailList $record) {
            return route('mailcoach.emailLists.summary', $record);
        };
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    public function mount(): void
    {
        $this->authorize('viewAny', static::getEmailListClass());
    }

    protected function getTableQuery(): Builder
    {
        return self::getEmailListClass()::query()
            ->select(self::getEmailListTableName().'.*')
            ->selectSub(
                query: self::getSubscriberClass()::query()
                    ->subscribed()
                    ->where('email_list_id', DB::raw(self::getEmailListTableName().'.id'))
                    ->select(DB::raw('count(*)')),
                as: 'active_subscribers_count'
            );
    }

    public function getTitle(): string
    {
        return __mc('Lists');
    }

    public function getLayoutData(): array
    {
        if (! Auth::user()->can('create', self::getEmailListClass())) {
            return [
                'hideBreadcrumbs' => true,
            ];
        }

        return [
            'create' => 'list',
            'hideBreadcrumbs' => true,
        ];
    }
}
