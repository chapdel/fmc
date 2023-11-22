<?php

namespace Spatie\Mailcoach\Livewire\Automations;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class AutomationSubscribersComponent extends TableComponent
{
    public Automation $automation;

    public function mount(Automation $automation)
    {
        app(MainNavigation::class)->activeSection()?->add($automation->name, route('mailcoach.automations'));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subscriber.email')
                ->url(fn ($record) => route('mailcoach.emailLists.subscriber.details', [$record->subscriber->emailList, $record->subscriber]))
                ->extraAttributes(['class' => 'link'])
                ->searchable(),
            TextColumn::make('current_step')
                ->html()
                ->getStateUsing(function (ActionSubscriber $record) {
                    $latestAction = $record->subscriber->actions->where('automation_id', $this->automation->id)->sortByDesc('id')->first();

                    if (! $latestAction) {
                        return '';
                    }

                    $index = $this->automation->allActions->search(fn ($action) => $action->uuid === $latestAction->uuid) + 1;

                    $status = match (true) {
                        ! is_null($record->halted_at) => __mc('halted'),
                        ! is_null($record->completed_at) => __mc('completed'),
                        default => __mc('active'),
                    };

                    return <<<"html"
                        <span>{$index} &ndash; {$latestAction->action::getName()} <span class="tag-neutral">{$status}</span></span>
                    html;
                }),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getActionSubscriberClass()::query()
            ->orderByDesc('created_at')
            ->with('subscriber.emailList', 'subscriber.actions')
            ->whereIn('action_id', $this->automation->allActions()->pluck('id'));
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.automations.layouts.automation';
    }

    public function getLayoutData(): array
    {
        return [
            'automation' => $this->automation,
            'title' => __mc('Subscribers'),
        ];
    }
}
