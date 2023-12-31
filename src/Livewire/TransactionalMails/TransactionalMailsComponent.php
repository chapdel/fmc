<?php

namespace Spatie\Mailcoach\Livewire\TransactionalMails;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class TransactionalMailsComponent extends TableComponent
{
    public function mount()
    {
        $this->authorize('viewAny', static::getTransactionalMailClass());
    }

    protected function getTableQuery(): Builder
    {
        return self::getTransactionalMailClass()::query();
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
                ->label(__mc('Name'))
                ->extraAttributes(['class' => 'link'])
                ->searchable(),
            TextColumn::make('contentItem.subject')
                ->label(__mc('Subject'))
                ->searchable(),
            TextColumn::make('to')
                ->sortable()
                ->label(__mc('To'))
                ->searchable(Mailcoach::isPostgresqlDatabase() ? '"to"' : true),
            TextColumn::make('cc')
                ->sortable()
                ->label(__mc('CC'))
                ->searchable(),
            TextColumn::make('bcc')
                ->sortable()
                ->label(__mc('BCC'))
                ->searchable(),
            IconColumn::make('store_mail')
                ->label(__mc('Store'))
                ->icons([
                    'heroicon-o-check-circle' => true,
                    'heroicon-o-x-circle' => false,
                ])
                ->color(fn (TransactionalMail $record) => match ($record->store_mail) {
                    true => 'success',
                    false => 'danger',
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (TransactionalMail $record) {
            return route('mailcoach.transactionalMails.templates.edit', $record);
        };
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (TransactionalMail $record) => $this->duplicateTransactionalMail($record))
                    ->icon('heroicon-o-clipboard')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(fn (TransactionalMail $record) => $record->delete())
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]),
        ];
    }

    public function duplicateTransactionalMail(TransactionalMail $transactionalMail)
    {
        $this->authorize('create', self::getTransactionalMailClass());

        /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $duplicateTemplate */
        $duplicateTemplate = self::getTransactionalMailClass()::create([
            'uuid' => Str::uuid(),
            'name' => $transactionalMail->name.'-copy',
            'from' => $transactionalMail->from,
            'cc' => $transactionalMail->cc,
            'to' => $transactionalMail->to,
            'bcc' => $transactionalMail->bcc,
            'type' => $transactionalMail->type,
            'replacers' => $transactionalMail->replacers,
            'store_mail' => $transactionalMail->store_mail,
            'test_using_mailable' => $transactionalMail->test_using_mailable,
        ]);

        $duplicateTemplate->contentItem->update([
            'subject' => $transactionalMail->contentItem->subject,
            'template_id' => $transactionalMail->contentItem->template_id,
            'html' => $transactionalMail->contentItem->html,
            'structured_html' => $transactionalMail->contentItem->structured_html,
            'utm_tags' => (bool) $transactionalMail->contentItem->utm_tags,
        ]);

        notify(__mc('Email :name was duplicated.', ['name' => $transactionalMail->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    public function getLayoutData(): array
    {
        if (Auth::user()->can('create', self::getTransactionalMailClass())) {
            return ['create' => 'transactional-template', 'createText' => __mc('Create email')];
        }

        return [];
    }
}
