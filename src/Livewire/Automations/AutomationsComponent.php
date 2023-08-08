<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Automation\Actions\DuplicateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class AutomationsComponent extends TableComponent
{
    protected function getTableQuery(): Builder
    {
        return self::getAutomationClass()::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('status')
                ->label(__mc('Run'))
                ->sortable()
                ->view('mailcoach::app.automations.columns.status'),
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('updated_at')
                ->sortable()
                ->date(config('mailcoach.date_format')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (Automation $record) => $this->duplicateAutomation($record))
                    ->icon('heroicon-o-clipboard')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(fn (Automation $record) => $record->delete())
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Automation $automation) {
            return route('mailcoach.automations.settings', $automation);
        };
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    public function toggleAutomationStatus(int $id)
    {
        $automation = self::getAutomationClass()::findOrFail($id);

        $automation->update([
            'status' => $automation->status === AutomationStatus::Paused
                ? AutomationStatus::Started
                : AutomationStatus::Paused,
        ]);

        $this->dispatch('notify', [
            'content' => __mc('Automation :automation was :status.', ['automation' => $automation->name, 'status' => $automation->status->value]),
        ]);
    }

    public function duplicateAutomation(Automation $automation)
    {
        /** @var DuplicateAutomationAction $action */
        $action = Mailcoach::getAutomationActionClass('duplicate_automation', DuplicateAutomationAction::class);
        $duplicateAutomation = $action->execute($automation);

        notify(__mc('Automation :automation was duplicated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $duplicateAutomation);
    }

    public function getTitle(): string
    {
        return __mc('Automations');
    }

    public function getLayoutData(): array
    {
        return [
            'create' => Auth::user()->can('create', self::getAutomationClass())
                ? 'automation'
                : null,
            'hideBreadcrumbs' => true,
        ];
    }
}
