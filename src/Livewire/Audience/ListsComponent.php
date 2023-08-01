<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
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
                ->view('mailcoach::app.emailLists.columns.activeSubscribersCount'),
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
        return function (EmailList $emailList) {
            return route('mailcoach.emailLists.summary', $emailList);
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
        return self::getEmailListClass()::query();
    }

    public function getTitle(): string
    {
        return __mc('Lists');
    }

    public function getLayoutData(): array
    {
        if (! Auth::user()->can('create', self::getEmailListClass())) {
            return [];
        }

        return [
            'create' => 'list',
        ];
    }
}
