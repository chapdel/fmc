<?php

namespace Spatie\Mailcoach\Livewire\Templates;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Livewire\FilamentDataTableComponent;

class TemplatesComponent extends FilamentDataTableComponent
{
    protected function getTableQuery(): Builder
    {
        return self::getTemplateClass()::query();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            IconColumn::make('contains_placeholders')
                ->label(__mc('Placeholders'))
                ->icons([
                    'heroicon-o-check-circle' => true,
                    'heroicon-o-x-circle' => false,
                ])
                ->color(fn (Template $template) => match ($template->contains_placeholders) {
                    true => 'success',
                    false => '',
                })->alignCenter(),
            TextColumn::make('created_at')
                ->sortable()
                ->alignRight()
                ->date(config('mailcoach.date_format')),
            TextColumn::make('updated_at')
                ->alignRight()
                ->sortable()
                ->date(config('mailcoach.date_format')),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Template $template) {
            return route('mailcoach.templates.edit', $template);
        };
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (Template $record) => $this->duplicateTemplate($record))
                    ->icon('heroicon-o-clipboard')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(fn (Template $record) => $record->delete())
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]),
        ];
    }

    public function duplicateTemplate(Template $template)
    {
        $this->authorize('create', self::getTemplateClass());

        $duplicateTemplate = self::getTemplateClass()::create([
            'name' => $template->name.' - '.__mc('copy'),
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        $this->flash(__mc('Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }

    public function getTitle(): string
    {
        return __mc('Templates');
    }
}
