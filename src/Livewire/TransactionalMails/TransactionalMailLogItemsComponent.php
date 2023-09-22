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
                ->icon(fn (TransactionalMailLogItem $item) => $item->fake ? 'heroicon-o-command-line' : 'heroicon-o-envelope')
                ->tooltip(fn (TransactionalMailLogItem $item) => $item->fake ? __mc('Fake send') : __mc('Sent'))
                ->color(fn (TransactionalMailLogItem $item) => $item->fake ? 'primary' : 'success'),
            TextColumn::make('contentItem.subject')
                ->extraAttributes(['class' => 'link'])
                ->label(__mc('Subject'))
                ->searchable(),
            TextColumn::make('to')
                ->getStateUsing(fn (TransactionalMailLogItem $item) => $item->toString())
                ->searchable(),
            TextColumn::make('contentItem.opens_count')->label(__mc('Opens'))->numeric(),
            TextColumn::make('contentItem.clicks_count')->label(__mc('Clicks'))->numeric(),
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

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (TransactionalMailLogItem $item) => route('mailcoach.transactionalMails.show', $item);
    }

    public function getTitle(): string
    {
        return __mc('Log');
    }
}
