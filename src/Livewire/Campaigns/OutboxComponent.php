<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Livewire\Content\ContentItemTable;

class OutboxComponent extends ContentItemTable
{
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sent_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableHeaderActions(): array
    {
        $count = $this->contentItems->sum(fn ($contentItem) => $contentItem->sends()->failed()->count());

        return [
            Action::make('failed_sends')
                ->label(__mc_choice('Retry :count failed send|Retry :count failed sends', $count, ['count' => $count]))
                ->action('retryFailedSends')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => $count === 0 || ! $this->model instanceof Campaign),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getSendClass()::query()
            ->with(['feedback', 'subscriber'])
            ->whereIn('content_item_id', $this->contentItems->pluck('id'))
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
                ->getStateUsing(fn (Send $record) => $record->subscriber?->email ?? '<'.__mc('deleted subscriber').'>'),
            TextColumn::make('failure_reason')
                ->label(__mc('Problem'))
                ->getStateUsing(fn (Send $record) => "{$record->failure_reason}{$record->latestFeedback()?->formatted_type}"),
            TextColumn::make('sent_at')
                ->label(__mc('Sent'))
                ->date(config('mailcoach.date_format'), config('mailcoach.timezone'))
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
                        title: "{$this->model->name} outbox",
                    );
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Send $record) {
            if ($record->subscriber) {
                return route('mailcoach.emailLists.subscriber.details', [$record->subscriber->emailList, $record->subscriber]);
            }

            return null;
        };
    }

    public function retryFailedSends()
    {
        $this->authorize('update', $this->model);

        $failedSendsCount = $this->contentItems->sum(fn ($contentItem) => $contentItem->sends()->failed()->count());

        if ($failedSendsCount === 0) {
            notifyError(__mc('There are no failed mails to resend anymore.'));

            return;
        }

        if (! $this->model instanceof Campaign) {
            return;
        }

        dispatch(new RetrySendingFailedSendsJob($this->model));

        notify(__mc('Retrying to send :failedSendsCount mails...', ['failedSendsCount' => $failedSendsCount]), 'warning');

        return redirect()->route('mailcoach.campaigns.summary', $this->model);
    }

    public function getTitle(): string
    {
        return __mc('Outbox');
    }
}
