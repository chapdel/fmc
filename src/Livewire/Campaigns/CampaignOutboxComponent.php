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
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignOutboxComponent extends TableComponent
{
    public Campaign $campaign;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sent_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        app(MainNavigation::class)->activeSection()?->add($this->campaign->name, route('mailcoach.campaigns'));
    }

    protected function getTableHeaderActions(): array
    {
        $count = $this->campaign->sends()->failed()->count();

        return [
            Action::make('failed_sends')
                ->label(__mc_choice('Retry :count failed send|Retry :count failed sends', $count, ['count' => $count]))
                ->action('retryFailedSends')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => $this->campaign->sends()->failed()->count() === 0),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getSendClass()::query()
            ->with(['feedback', 'campaign', 'subscriber'])
            ->where('campaign_id', $this->campaign->id)
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
                        title: "{$this->campaign->name} outbox",
                    );
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Send $send) {
            if ($send->subscriber) {
                return route('mailcoach.emailLists.subscriber.details', [$this->campaign->emailList, $send->subscriber]);
            }

            return null;
        };
    }

    public function retryFailedSends()
    {
        $this->authorize('update', $this->campaign);

        $failedSendsCount = $this->campaign->sends()->failed()->count();

        if ($failedSendsCount === 0) {
            notify(__mc('There are no failed mails to resend anymore.'), 'error');

            return;
        }

        dispatch(new RetrySendingFailedSendsJob($this->campaign));

        notify(__mc('Retrying to send :failedSendsCount mails...', ['failedSendsCount' => $failedSendsCount]), 'warning');

        return redirect()->route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function getTitle(): string
    {
        return __mc('Outbox');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.campaigns.layouts.campaign';
    }

    public function getLayoutData(): array
    {
        return [
            'campaign' => $this->campaign,
        ];
    }
}
