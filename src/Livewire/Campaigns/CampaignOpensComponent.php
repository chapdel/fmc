<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignOpensComponent extends TableComponent
{
    public Campaign $campaign;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'first_opened_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        if (! is_null($this->campaign->open_count)) {
            return __mc('No opens yet. Stay tuned.');
        }

        return __mc('No opens tracked');
    }

    protected function getTableQuery(): Builder
    {
        $prefix = DB::getTablePrefix();
        $campaignOpenTable = static::getCampaignOpenTableName();
        $subscriberTableName = static::getSubscriberTableName();
        $emailListTableName = static::getEmailListTableName();

        return self::getCampaignOpenClass()::query()
            ->selectRaw("
                {$prefix}{$subscriberTableName}.uuid as subscriber_uuid,
                {$prefix}{$emailListTableName}.uuid as subscriber_email_list_uuid,
                {$prefix}{$subscriberTableName}.email as subscriber_email,
                count({$prefix}{$campaignOpenTable}.subscriber_id) as open_count,
                min({$prefix}{$campaignOpenTable}.created_at) AS first_opened_at
            ")
            ->join(static::getCampaignTableName(), static::getCampaignTableName().'.id', '=', "{$campaignOpenTable}.campaign_id")
            ->join($subscriberTableName, "{$subscriberTableName}.id", '=', "{$campaignOpenTable}.subscriber_id")
            ->join($emailListTableName, "{$subscriberTableName}.email_list_id", '=', "{$emailListTableName}.id")
            ->where(static::getCampaignTableName().'.id', $this->campaign->id)
            ->groupBy("{$prefix}{$subscriberTableName}.uuid", "{$prefix}{$emailListTableName}.uuid", "{$prefix}{$subscriberTableName}.email");
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->subscriber_uuid;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return self::getCampaignOpenClass()::count() > $this->getTableRecordsPerPageSelectOptions()[0];
    }

    protected function getTableColumns(): array
    {
        $prefix = DB::getTablePrefix();
        $subscriberTableName = static::getSubscriberTableName();

        return [
            TextColumn::make("{$prefix}{$subscriberTableName}.email")
                ->getStateUsing(fn ($record) => $record->subscriber_email)
                ->label(__mc('Email'))
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('open_count')
                ->label(__mc('Opens'))
                ->sortable()
                ->numeric()
                ->alignRight(),
            TextColumn::make('first_opened_at')
                ->label(__mc('First opened at'))
                ->getStateUsing(fn ($record) => Carbon::parse($record->first_opened_at)->toMailcoachFormat())
                ->alignRight()
                ->sortable(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            return route('mailcoach.emailLists.subscriber.details', [$record->subscriber_email_list_uuid, $record->subscriber_uuid]);
        };
    }

    protected function getTableBulkActions(): array
    {
        $prefix = DB::getTablePrefix();
        $subscriberTableName = static::getSubscriberTableName();

        return [
            BulkAction::make('export')
                ->label(__mc('Export selected'))
                ->icon('heroicon-o-cloud-arrow-down')
                ->action(function () use ($prefix, $subscriberTableName) {
                    $header = [
                        'email',
                        'opens',
                        'first_opened_at',
                    ];

                    $rows = $this->getTableQuery()->whereIn("{$prefix}{$subscriberTableName}.uuid", $this->selectedTableRecords)->get();

                    return $this->export(
                        header: $header,
                        rows: $rows,
                        formatRow: function (CampaignOpen $row) {
                            return [
                                'email' => $row->subscriber_email,
                                'opens' => $row->open_count,
                                'first_opened_at' => $row->first_opened_at->toMailcoachFormat(),
                            ];
                        },
                        title: $this->getTitle(),
                    );
                }),
        ];
    }

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        app(MainNavigation::class)->activeSection()?->add($this->campaign->name, route('mailcoach.campaigns'));
    }

    public function getTitle(): string
    {
        return __mc('Opens');
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
