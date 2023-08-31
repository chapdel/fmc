<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
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
            TextColumn::make('reason')
                ->label(__mc('Reason'))
                ->sortable()
                ->searchable(),
            TextColumn::make('origin')
                ->label(__mc('Origin'))
                ->sortable()
                ->searchable(),
            //->view('mailcoach::app.tableField'),
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

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Suppression::class)
                ->form([
                    TextInput::make('email')
                        ->required()
                        ->email(),
                ])->createAnother(false)
                ->action(function (array $data) {
                    Suppression::fromAdmin($data['email']);

                    notify(__mc("Added `{$data['email']}` to the suppression list successfully"));
                }),
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
