<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Support\LinkHasher;

class ClicksComponent extends ContentItemTable
{
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'unique_click_count';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTitle(): string
    {
        return __mc('Clicks');
    }

    protected function getTableQuery(): Builder
    {
        return $this->contentItem->links()->getQuery();
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (! is_null($this->contentItem->click_count)) {
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
            $this->contentItem->add_subscriber_link_tags
                ? TextColumn::make('tag')
                    ->label(__mc('Tag'))
                    ->getStateUsing(fn (Link $link) => '<span class="tag-neutral">'.LinkHasher::hash($this->contentItem->model, $link->url).'</span>')
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
        return function (Link $link) {
            return match (true) {
                $this->contentItem->model instanceof Campaign => route('mailcoach.campaigns.link-clicks', [$this->contentItem->model, $link]),
                $this->contentItem->model instanceof AutomationMail => route('mailcoach.automations.mails.link-clicks', [$this->contentItem->model, $link]),
            };
        };
    }
}
