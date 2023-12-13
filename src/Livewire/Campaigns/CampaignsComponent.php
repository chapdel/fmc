<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\Mailcoach;

class CampaignsComponent extends TableComponent
{
    public function getTableQuery(): Builder
    {
        $prefix = DB::getTablePrefix();
        $campaignsTable = self::getCampaignTableName();

        return self::getCampaignClass()::query()
            ->select(self::getCampaignTableName().'.*')
            ->with(['emailList', 'contentItems'])
            ->addSelect(
                DB::raw(Mailcoach::isPostgresqlDatabase()
                ? <<<"SQL"
                    CASE
                        WHEN status = 'draft' AND scheduled_at IS NULL THEN '2999-01-01'::timestamp + INTERVAL '1 day' * {$prefix}{$campaignsTable}.id
                        WHEN {$prefix}{$campaignsTable}.scheduled_at IS NOT NULL THEN {$prefix}{$campaignsTable}.scheduled_at
                        WHEN {$prefix}{$campaignsTable}.sent_at IS NOT NULL THEN {$prefix}{$campaignsTable}.sent_at
                        ELSE {$prefix}{$campaignsTable}.updated_at
                    END as sent_sort
                SQL
                : <<<"SQL"
                    CASE
                        WHEN status = 'draft' AND scheduled_at IS NULL THEN CONCAT(999999999, {$prefix}{$campaignsTable}.id)
                        WHEN {$prefix}{$campaignsTable}.scheduled_at IS NOT NULL THEN {$prefix}{$campaignsTable}.scheduled_at
                        WHEN {$prefix}{$campaignsTable}.sent_at IS NOT NULL THEN {$prefix}{$campaignsTable}.sent_at
                        ELSE {$prefix}{$campaignsTable}.updated_at
                    END as 'sent_sort'
                SQL
                )
            );
    }

    public function getTableGrouping(): ?Group
    {
        return Group::make('status')
            ->label('');
    }

    public function getTable(): Table
    {
        return parent::getTable()
            ->poll(function () {
                if ($this->getTableRecords()->where('status', CampaignStatus::Sending)->count() > 0) {
                    return '10s';
                }

                return null;
            });
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('Status')
                ->label('')
                ->extraAttributes([
                    'class' => 'px-1 py-2',
                ])
                ->getStateUsing(fn (Campaign $record) => $record->status->value)
                ->icon(fn (Campaign $record) => match (true) {
                    $record->isScheduled() => 'heroicon-o-clock',
                    $record->status === CampaignStatus::Draft => 'heroicon-o-pencil-square',
                    $record->status === CampaignStatus::Sent => 'heroicon-o-check-circle',
                    $record->status === CampaignStatus::Sending && $record->isSplitTestStarted() && ! $record->hasSplitTestWinner() => 'heroicon-o-pause',
                    $record->status === CampaignStatus::Sending => 'heroicon-o-arrow-path',
                    $record->status === CampaignStatus::Cancelled => 'heroicon-o-x-circle',
                    default => '',
                })
                ->extraAttributes(function (Campaign $record) {
                    if ($record->status === CampaignStatus::Sending && $record->isSplitTestStarted() && ! $record->hasSplitTestWinner()) {
                        return [];
                    }

                    if ($record->status === CampaignStatus::Sending) {
                        return ['class' => 'fa-spin'];
                    }

                    return [];
                })
                ->tooltip(fn (Campaign $record) => match (true) {
                    $record->isScheduled() => __mc('Scheduled'),
                    $record->status === CampaignStatus::Sent => __mc('Sent'),
                    $record->status === CampaignStatus::Draft => __mc('Draft'),
                    $record->status === CampaignStatus::Sending && $record->isSplitTestStarted() && ! $record->hasSplitTestWinner() => __mc('Awaiting split results'),
                    $record->status === CampaignStatus::Sending => __mc('Sending'),
                    $record->status === CampaignStatus::Cancelled => __mc('Cancelled'),
                    default => '',
                })
                ->color(fn (Campaign $record) => match (true) {
                    $record->isScheduled() => 'warning',
                    $record->status === CampaignStatus::Draft => '',
                    $record->status === CampaignStatus::Sent => 'success',
                    $record->status === CampaignStatus::Sending => 'warning',
                    $record->status === CampaignStatus::Cancelled => 'danger',
                    default => '',
                })
                ->alignCenter(),
            TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->view('mailcoach::app.campaigns.columns.name'),
            TextColumn::make('List')
                ->sortable(query: function (Builder $query, $direction) {
                    $query->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getCampaignTableName().'.email_list_id')
                        ->orderBy(self::getEmailListTableName().'.name', $direction);
                })
                ->url(fn (Campaign $record) => $record->emailList
                    ? route('mailcoach.emailLists.summary', $record->emailList)
                    : null
                )
                ->view('mailcoach::app.campaigns.columns.email_list'),
            TextColumn::make('emails')
                ->label(__mc('Emails'))
                ->alignRight()
                ->numeric()
                ->view('mailcoach::app.campaigns.columns.sends'),
            TextColumn::make('unique_open_count')
                ->label(__mc('Opens'))
                ->alignRight()
                ->numeric()
                ->view('mailcoach::app.campaigns.columns.opens'),
            TextColumn::make('unique_click_count')
                ->alignRight()
                ->numeric()
                ->label(__mc('Clicks'))
                ->view('mailcoach::app.campaigns.columns.clicks'),
            TextColumn::make('sent_sort')
                ->label(__mc('Sent'))
                ->alignRight()
                ->sortable()
                ->view('mailcoach::app.campaigns.columns.sent'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options([
                    'sent' => 'Sent',
                    'scheduled' => 'Scheduled',
                    'sending' => 'Sending',
                    'draft' => 'Draft',
                ])
                ->query(function (Builder $query, array $data) {
                    return match ($data['value']) {
                        'sent' => $query->sent(),
                        'scheduled' => $query->scheduled(),
                        'sending' => $query->sending(),
                        'draft' => $query->draft(),
                        default => $query,
                    };
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(function (Campaign $record) {
                        $this->duplicateCampaign($record);
                    })
                    ->icon('heroicon-o-clipboard')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(function (Campaign $record) {
                        $record->delete();
                        notify(__mc('Campaign :campaign was deleted.', ['campaign' => $record->name]));
                    })
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('delete')
                ->requiresConfirmation()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->deselectRecordsAfterCompletion()
                ->action(function (Collection $records) {
                    $records->each->delete();
                    notify(__mc('Campaigns successfully deleted.'));
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Campaign $record) {
            if ($record->isSent() || $record->isSending() || $record->isCancelled()) {
                return route('mailcoach.campaigns.summary', $record);
            }

            if ($record->isScheduled()) {
                return route('mailcoach.campaigns.delivery', $record);
            }

            return route('mailcoach.campaigns.content', $record);
        };
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sent_sort';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function duplicateCampaign(Campaign $campaign)
    {
        $this->authorize('create', self::getCampaignClass());

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $duplicateCampaign */
        $duplicateCampaign = self::getCampaignClass()::create([
            'name' => __mc('Duplicate of').' '.$campaign->name,
            'email_list_id' => $campaign->email_list_id,
            'segment_class' => $campaign->segment_class,
            'segment_id' => $campaign->segment_id,
        ]);
        $duplicateCampaign->contentItem->delete();

        foreach ($campaign->contentItems as $contentItem) {
            $duplicateCampaign->contentItem->create([
                'model_id' => $duplicateCampaign->id,
                'model_type' => $duplicateCampaign->getMorphClass(),
                'subject' => $contentItem->subject,
                'template_id' => $contentItem->template_id,
                'html' => $contentItem->html,
                'structured_html' => $contentItem->structured_html,
                'utm_tags' => (bool) $contentItem->utm_tags,
            ]);
        }

        $duplicateCampaign->update([
            'segment_description' => $duplicateCampaign->getSegment()->description(),
        ]);

        notify(__mc('Campaign :campaign was duplicated.', ['campaign' => $campaign->name]));

        $this->redirect(route('mailcoach.campaigns.settings', $duplicateCampaign));
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Campaigns'),
            'create' => Auth::user()->can('create', self::getCampaignClass())
                ? 'campaign'
                : null,
            'hideBreadcrumbs' => true,
        ];
    }
}
