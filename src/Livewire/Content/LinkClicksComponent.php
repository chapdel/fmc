<?php

namespace Spatie\Mailcoach\Livewire\Content;

use Closure;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Content\Models\Click;

class LinkClicksComponent extends ContentItemTable
{
    public string $linkUuids;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'first_clicked_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function getTitle(): string
    {
        $firstUuid = explode(',', $this->linkUuids)[0];

        return str_replace(['https://', 'http://'], '', self::getLinkClass()::findByUuid($firstUuid)->url).' '.__mc('clicks');
    }

    protected function getTableQuery(): Builder
    {
        $prefix = DB::getTablePrefix();

        $ClickTable = static::getClickTableName();
        $subscriberTableName = static::getSubscriberTableName();
        $emailListTableName = static::getEmailListTableName();

        return static::getClickClass()::query()
            ->selectRaw("
                {$prefix}{$subscriberTableName}.uuid as subscriber_uuid,
                {$prefix}{$emailListTableName}.uuid as subscriber_email_list_uuid,
                {$prefix}{$subscriberTableName}.email as subscriber_email,
                count({$prefix}{$ClickTable}.subscriber_id) as click_count,
                min({$prefix}{$ClickTable}.created_at) AS first_clicked_at
            ")
            ->join(static::getLinkTableName(), static::getLinkTableName().'.id', '=', "{$ClickTable}.link_id")
            ->join($subscriberTableName, "{$subscriberTableName}.id", '=', "{$ClickTable}.subscriber_id")
            ->join($emailListTableName, "{$subscriberTableName}.email_list_id", '=', "{$emailListTableName}.id")
            ->whereIn(static::getLinkTableName().'.uuid', explode(',', $this->linkUuids))
            ->groupBy("{$prefix}{$subscriberTableName}.uuid", "{$prefix}{$emailListTableName}.uuid", "{$prefix}{$subscriberTableName}.email");
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subscriber_email')
                ->label(__mc('Email'))
                ->sortable()
                ->extraAttributes(['class' => 'link'])
                ->searchable(query: function (Builder $query, string $search) {
                    $query->where('mailcoach_subscribers.email', 'like', '%'.$search.'%');
                }),
            TextColumn::make('click_count')
                ->label(__mc('Clicks'))
                ->sortable()
                ->numeric(),
            TextColumn::make('first_clicked_at')
                ->sortable()
                ->label(__mc('First clicked at'))
                ->dateTime(config('mailcoach.date_format')),
        ];
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->subscriber_uuid;
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
                        'clicks',
                        'first_clicked_at',
                    ];

                    $rows = $this->getTableQuery()->whereIn("{$prefix}{$subscriberTableName}.uuid", $this->selectedTableRecords)->get();

                    return $this->export(
                        header: $header,
                        rows: $rows,
                        formatRow: function (Click $row) {
                            return [
                                'email' => $row->subscriber_email,
                                'clicks' => $row->click_count,
                                'first_clicked_at' => $row->first_clicked_at->toMailcoachFormat(),
                            ];
                        },
                        title: $this->getTitle(),
                    );
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function ($record) {
            return route('mailcoach.emailLists.subscriber.details', [$record->subscriber_email_list_uuid, $record->subscriber_uuid]);
        };
    }
}
