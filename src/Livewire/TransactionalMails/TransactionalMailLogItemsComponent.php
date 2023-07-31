<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Livewire\TableComponent;

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
            ->withCount(['opens', 'clicks']);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subject')
                ->label(__mc('Subject'))
                ->searchable(),
            TextColumn::make('to')
                ->getStateUsing(fn (TransactionalMailLogItem $item) => $item->toString())
                ->searchable(),
            TextColumn::make('opens_count')->label(__mc('Opens'))->numeric(),
            TextColumn::make('clicks_count')->label(__mc('Clicks'))->numeric(),
            TextColumn::make('created_at')
                ->label(__mc('Sent'))
                ->sortable()
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

    public function getTitle(): string
    {
        return __mc('Log');
    }
}
