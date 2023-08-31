<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\TableComponent;

class SuppressionListComponent extends TableComponent
{
    use UsesMailcoachModels;

    public function getTableQuery(): Builder
    {
        return self::getSuppressionClass()::query();
    }

    public function getTitle(): string
    {
        return __mc('Suppression List'); // @todo why is this not showing up?
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('email')
                ->label(__mc('Email'))
                ->sortable()
                ->searchable(),
            //->view('mailcoach::app.tableField'),
            TextColumn::make('stream')
                ->label(__mc('Stream'))
                ->sortable()
                ->searchable(),
            //->view('mailcoach::app.tableField'),
            TextColumn::make('reason')
                ->label(__mc('Reason'))
                ->sortable()
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Reactivate')
                ->button()
                ->action(fn (Suppression $record) => $this->reactivate($record))
                ->requiresConfirmation()
                ->label(__mc('Reactivate')),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function reactivate(Suppression $suppression): void
    {
        $suppression->delete();

        Notification::make()
            ->title("Reactivated `{$suppression->email}` successfully")
            ->success()
            ->send();
    }
}
