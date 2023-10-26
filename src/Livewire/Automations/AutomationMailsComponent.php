<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\Mailcoach\Domain\Automation\Models\Action as ActionModel;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Livewire\TableComponent;

class AutomationMailsComponent extends TableComponent
{
    protected function getTableQuery(): Builder
    {
        return self::getAutomationMailClass()::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->label(__mc('Name'))
                ->size('base')
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('sent_to_number_of_subscribers')
                ->sortable()
                ->label(__mc('Emails'))
                ->numeric()
                ->size('base')
                ->getStateUsing(fn (AutomationMail $record) => number_format($record->contentItem->sent_to_number_of_subscribers) ?: '–'),
            TextColumn::make('unique_open_count')
                ->sortable()
                ->label(__mc('Opens'))
                ->numeric()
                ->view('mailcoach::app.automations.mails.columns.opens'),
            TextColumn::make('unique_click_count')
                ->sortable()
                ->label(__mc('Clicks'))
                ->numeric()
                ->view('mailcoach::app.automations.mails.columns.clicks'),
            TextColumn::make('created_at')
                ->sortable()
                ->label(__mc('Created'))
                ->size('base')
                ->extraAttributes([
                    'class' => 'tabular-nums',
                ])
                ->date(config('mailcoach.date_format')),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('automation_uuid')
                ->label(__mc('Automation'))
                ->options(function () {
                    return self::getAutomationClass()::pluck('name', 'uuid');
                })
                ->multiple()
                ->query(function (Builder $query, array $data) {
                    if (! $data['values']) {
                        return;
                    }

                    $class = self::getAutomationMailClass();
                    $shortname = (new ReflectionClass(new $class))->getShortName();

                    $automationMailIds = self::getAutomationActionClass()::query()
                        ->whereHas('automation', fn (Builder $query) => $query->whereIn('uuid', $data['values']))
                        ->whereRaw('FROM_BASE64(action) like \'%'.$shortname.'%\'')
                        ->get()
                        ->map(function (ActionModel $action) use ($shortname) {
                            /**
                             * We want to get any action that has an automation email
                             * referenced. Therefore, we need to parse serialized
                             * string of the action to get the model identifier.
                             */
                            $rawAction = base64_decode($action->getRawOriginal('action'));
                            $idPart = Str::after($rawAction, $shortname.'";s:2:"id";i:');
                            $id = Str::before($idPart, ';');

                            return (int) $id;
                        });

                    $query->whereIn('id', $automationMailIds);
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (AutomationMail $record) => $this->duplicateAutomationMail($record))
                    ->icon('heroicon-o-clipboard')
                    ->label(__mc('Duplicate'))
                    ->hidden(fn (AutomationMail $record) => ! Auth::user()->can('create', self::getAutomationMailClass())),
                Action::make('Delete')
                    ->action(fn (AutomationMail $record) => $record->delete())
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->hidden(fn (AutomationMail $record) => ! Auth::user()->can('delete', self::getAutomationMailClass())),
            ]),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (AutomationMail $record) {
            return route('mailcoach.automations.mails.summary', $record);
        };
    }

    public function duplicateAutomationMail(AutomationMail $automationMail)
    {
        $this->authorize('create', $automationMail);

        /** @var AutomationMail $newAutomationMail */
        $newAutomationMail = self::getAutomationMailClass()::create([
            'name' => __mc('Duplicate of').' '.$automationMail->name,
        ]);

        $newAutomationMail->contentItem->update([
            'subject' => $automationMail->contentItem->subject,
            'template_id' => $automationMail->contentItem->template_id,
            'html' => $automationMail->contentItem->html,
            'structured_html' => $automationMail->contentItem->structured_html,
            'webview_html' => $automationMail->contentItem->webview_html,
            'utm_tags' => $automationMail->contentItem->utm_tags,
        ]);

        notify(__mc('Email :name was duplicated.', ['name' => $newAutomationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $newAutomationMail);
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    public function getLayoutData(): array
    {
        return [
            'create' => 'automation-mail',
            'createText' => __mc('Create automation mail'),
        ];
    }
}
