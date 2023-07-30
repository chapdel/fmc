<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailOutboxComponent extends TableComponent
{
    public AutomationMail $automationMail;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sent_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function mount(AutomationMail $automationMail)
    {
        $this->automationMail = $automationMail;

        app(MainNavigation::class)->activeSection()?->add($this->automationMail->name, route('mailcoach.automations.mails'));
    }

    protected function getTableQuery(): Builder
    {
        return self::getSendClass()::query()
            ->with(['feedback', 'automationMail', 'subscriber'])
            ->where('automation_mail_id', $this->automationMail->id)
            ->whereNull('invalidated_at');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subscriber.email')
                ->label(__mc('Email'))
                ->extraAttributes(['class' => 'link'])
                ->searchable()
                ->sortable()
                ->getStateUsing(fn (Send $send) => $send->subscriber?->email ?? '<'.__mc('deleted subscriber').'>'),
            TextColumn::make('failure_reason')
                ->label(__mc('Problem'))
                ->getStateUsing(fn (Send $send) => "{$send->failure_reason}{$send->latestFeedback()?->formatted_type}"),
            TextColumn::make('sent_at')
                ->label(__mc('Sent'))
                ->sortable()
                ->alignRight(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->options([
                    'pending' => __mc('Pending'),
                    'failed' => __mc('Failed'),
                    'sent' => __mc('Sent'),
                    'bounced' => __mc('Bounced'),
                    'complained' => __mc('Complained'),
                ])
                ->query(function (Builder $query, array $data) {
                    return match ($data['value']) {
                        'pending' => $query->pending(),
                        'failed' => $query->failed(),
                        'sent' => $query->sent(),
                        'bounced' => $query->bounced(),
                        'complained' => $query->complained(),
                        default => $query,
                    };
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('export')
                ->label(__mc('Export selected'))
                ->icon('heroicon-o-cloud-arrow-down')
                ->action(function (Collection $rows) {
                    $header = [
                        'email',
                        'problem',
                        'sent',
                    ];

                    return $this->export(
                        header: $header,
                        rows: $rows,
                        formatRow: function (Send $send) {
                            return [
                                'email' => $send->subscriber?->email ?? '<deleted subscriber>',
                                'problem' => "{$send->failure_reason}{$send->latestFeedback()?->formatted_type}",
                                'sent' => $send->sent_at->toMailcoachFormat(),
                            ];
                        },
                        title: "{$this->automationMail->name} outbox",
                    );
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Send $send) {
            if ($send->subscriber) {
                return route('mailcoach.emailLists.subscriber.details', [$send->subscriber->emailList, $send->subscriber]);
            }

            return null;
        };
    }

    public function getTitle(): string
    {
        return __mc('Outbox');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.automations.mails.layouts.automationMail';
    }

    public function getLayoutData(): array
    {
        return [
            'mail' => $this->automationMail,
        ];
    }
}
