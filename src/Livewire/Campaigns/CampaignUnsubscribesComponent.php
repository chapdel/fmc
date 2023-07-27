<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Livewire\FilamentDataTableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignUnsubscribesComponent extends FilamentDataTableComponent
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
}
