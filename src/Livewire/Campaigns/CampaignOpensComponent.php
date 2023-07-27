<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\FilamentDataTableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignOpensComponent extends FilamentDataTableComponent
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
                ->getStateUsing(function ($record) {
                    if (config('mailcoach.encryption.enabled')) {
                        $subscriberClass = self::getSubscriberClass();
                        $subscriber = (new $subscriberClass(['email' => $record->subscriber_email, 'first_name' => null, 'last_name' => null]));
                        $subscriber->decryptRow();

                        return $subscriber->email;
                    }

                    return $record->subscriber_email;
                })
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
