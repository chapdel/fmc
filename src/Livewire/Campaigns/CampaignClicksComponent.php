<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignClicksComponent extends TableComponent
{
    public Campaign $campaign;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'unique_click_count';
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
        return __mc('Clicks');
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
        return $this->campaign->links()->getQuery();
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (! is_null($this->campaign->click_count)) {
            return __mc('No clicks yet. Stay tuned.');
        }

        return __mc('No clicks tracked');
    }

    protected function getTableColumns(): array
    {
        return array_filter([
            TextColumn::make('url')
                ->label(__mc('Link'))
                ->sortable()
                ->extraAttributes(['class' => 'link'])
                ->searchable(),
            $this->campaign->add_subscriber_link_tags
                ? TextColumn::make('tag')
                    ->label(__mc('Tag'))
                    ->getStateUsing(fn (CampaignLink $link) => '<span class="tag-neutral">'.LinkHasher::hash($this->campaign, $link->url).'</span>')
                    ->html()
                : null,
            TextColumn::make('unique_click_count')
                ->label(__mc('Unique clicks'))
                ->sortable()
                ->alignRight()
                ->numeric(),
            TextColumn::make('click_count')
                ->label(__mc('Clicks'))
                ->sortable()
                ->alignRight()
                ->numeric(),
        ]);
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (CampaignLink $link) {
            return route('mailcoach.campaigns.link-clicks', [$this->campaign, $link]);
        };
    }
}
