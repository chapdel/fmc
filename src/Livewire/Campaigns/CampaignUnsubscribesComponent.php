<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignUnsubscribesComponent extends TableComponent
{
    public Campaign $campaign;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
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

    public function getTitle(): string
    {
        return __mc('Unsubscribes');
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

    protected function getTableQuery(): Builder
    {
        return $this->campaign->unsubscribes()->with(['subscriber'])->getQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subscriber.email')
                ->label(__mc('Email'))
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('subscriber.first_name')
                ->label(__mc('First name'))
                ->sortable()
                ->searchable(),
            TextColumn::make('subscriber.last_name')
                ->label(__mc('Last name'))
                ->sortable()
                ->searchable(),
            TextColumn::make('created_at')
                ->label(__mc('Date'))
                ->sortable()
                ->alignRight(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (CampaignUnsubscribe $unsubscribe) {
            return route('mailcoach.emailLists.subscriber.details', [$this->campaign->emailList, $unsubscribe->subscriber]);
        };
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
                        'first_name',
                        'last_name',
                        'unsubscribed_at',
                    ];

                    return $this->export(
                        header: $header,
                        rows: $rows,
                        formatRow: function (CampaignUnsubscribe $unsubscribe) {
                            return [
                                'email' => $unsubscribe->subscriber?->email ?? '<deleted subscriber>',
                                'first_name' => $unsubscribe->subscriber ? $unsubscribe->subscriber->first_name : '<deleted subscriber>',
                                'last_name' => $unsubscribe->subscriber ? $unsubscribe->subscriber->last_name : '<deleted subscriber>',
                                'unsubscribed_at' => $unsubscribe->created_at->toMailcoachFormat(),
                            ];
                        },
                        title: "{$this->campaign->name} unsubscribes",
                    );
                }),
        ];
    }
}
