<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailClicksComponent extends TableComponent
{
    public AutomationMail $automationMail;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'unique_click_count';
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

    public function getTitle(): string
    {
        return __mc('Clicks');
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

    protected function getTableQuery(): Builder
    {
        return $this->automationMail->links()->getQuery();
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (! is_null($this->automationMail->click_count)) {
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
            $this->automationMail->add_subscriber_link_tags
                ? TextColumn::make('tag')
                    ->label(__mc('Tag'))
                    ->getStateUsing(fn (AutomationMailLink $link) => '<span class="tag-neutral">'.LinkHasher::hash($this->automationMail, $link->url).'</span>')
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
        return function (AutomationMailLink $link) {
            return route('mailcoach.automations.mails.link-clicks', [$this->automationMail, $link]);
        };
    }
}
