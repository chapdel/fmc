<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionReason;
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
        return __mc('Suppressions');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Suppressions'),
            'create' => 'suppression',
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('email')
                ->label(__mc('Email'))
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
                ->icon('heroicon-o-check-circle')
                ->action(fn (Suppression $record) => $this->reactivate($record))
                ->requiresConfirmation()
                ->hidden(fn (Suppression $record) => $record->reason === SuppressionReason::spamComplaint)
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

        notify("Reactivated `{$suppression->email}` successfully");
    }
}
