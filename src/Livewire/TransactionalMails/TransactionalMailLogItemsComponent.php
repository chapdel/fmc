<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class TransactionalMailLogItemsComponent extends TableComponent
{
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
        return self::getTransactionalMailLogItemClass()::query()
            ->with(['contentItem' => function (Builder $query) {
                $query->withCount(['opens', 'clicks']);
            }]);
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('fake')
                ->label('')
                ->icon(fn (TransactionalMailLogItem $record) => $record->fake ? 'heroicon-o-command-line' : 'heroicon-o-envelope')
                ->tooltip(fn (TransactionalMailLogItem $record) => $record->fake ? __mc('Fake send') : __mc('Sent'))
                ->color(fn (TransactionalMailLogItem $record) => $record->fake ? 'primary' : 'success'),
            TextColumn::make('contentItem.subject')
                ->extraAttributes(['class' => 'link'])
                ->size('base')
                ->label(__mc('Subject'))
                ->searchable(),
            TextColumn::make('to')
                ->size('base')
                ->getStateUsing(fn (TransactionalMailLogItem $record) => $record->toString())
                ->searchable(Mailcoach::isPostgresqlDatabase() ? '"to"' : true),
            TextColumn::make('contentItem.opens_count')->size('base')->label(__mc('Opens'))->numeric(),
            TextColumn::make('contentItem.clicks_count')->size('base')->label(__mc('Clicks'))->numeric(),
            TextColumn::make('created_at')
                ->label(__mc('Sent'))
                ->sortable()
                ->size('base')
                ->extraAttributes([
                    'class' => 'tabular-nums',
                ])
                ->date(config('mailcoach.date_format')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Delete')
                ->action(fn (TransactionalMailLogItem $record) => $record->delete())
                ->requiresConfirmation()
                ->label(' ')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('delete')
                ->requiresConfirmation()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->deselectRecordsAfterCompletion()
                ->action(fn (Collection $records) => $records->each->delete()),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (TransactionalMailLogItem $record) => route('mailcoach.transactionalMails.show', $record);
    }

    public function getTitle(): string
    {
        return __mc('Log');
    }
}
