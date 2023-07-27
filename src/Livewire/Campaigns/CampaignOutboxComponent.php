<?php

namespace Spatie\Mailcoach\Livewire\Campaigns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\App\Queries\CampaignSendsQuery;
use Spatie\Mailcoach\Livewire\FilamentDataTableComponent;
use Spatie\Mailcoach\MainNavigation;

class CampaignOutboxComponent extends FilamentDataTableComponent
{
    public Campaign $campaign;

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sent_at';
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

    protected function getTableHeaderActions(): array
    {
        $count = $this->campaign->sends()->failed()->count();

        return [
            Action::make('failed_sends')
                ->label(__mc_choice('Retry :count failed send|Retry :count failed sends', $count, ['count' => $count]))
                ->action('retryFailedSends')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => $this->campaign->sends()->failed()->count() === 0),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $subscriberTableName = self::getSubscriberTableName();

        return self::getSendClass()::query()
            ->with(['feedback', 'campaign', 'subscriber'])
            ->where('campaign_id', $this->campaign->id)
            ->whereNull('invalidated_at');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('subscriber.email')
                ->label(__mc('Email'))
                ->extraAttributes(['class' => 'link'])
                ->searchable()
                ->sortable()
                ->getStateUsing(fn (Send $send) => $send->subscriber?->email ?? '<'.__mc('deleted subscriber').'>'),
            TextColumn::make('failure_reason')
                ->label(__mc('Problem'))
                ->getStateUsing(fn (Send $send) => "{$send->failure_reason}{$send->latestFeedback()?->formatted_type}"),
            TextColumn::make('sent_at')
                ->label(__mc('Sent'))
                ->sortable()
                ->alignRight(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->options([
                    'pending' => __mc('Pending'),
                    'failed' => __mc('Failed'),
                    'sent' => __mc('Sent'),
                    'bounced' => __mc('Bounced'),
                    'complained' => __mc('Complained'),
                ])
                ->query(function (Builder $query, array $data) {
                    return match ($data['value']) {
                        'pending' => $query->pending(),
                        'failed' => $query->failed(),
                        'sent' => $query->sent(),
                        'bounced' => $query->bounced(),
                        'complained' => $query->complained(),
                        default => $query,
                    };
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Send $send) {
            if ($send->subscriber) {
                return route('mailcoach.emailLists.subscriber.details', [$this->campaign->emailList, $send->subscriber]);
            }

            return null;
        };
    }

    public function retryFailedSends()
    {
        $this->authorize('update', $this->campaign);

        $failedSendsCount = $this->campaign->sends()->failed()->count();

        if ($failedSendsCount === 0) {
            $this->flash(__mc('There are no failed mails to resend anymore.'), 'error');

            return;
        }

        dispatch(new RetrySendingFailedSendsJob($this->campaign));

        $this->flash(__mc('Retrying to send :failedSendsCount mails...', ['failedSendsCount' => $failedSendsCount]), 'warning');

        return redirect()->route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function getTitle(): string
    {
        return __mc('Outbox');
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

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->campaign);

        $sendsQuery = (new CampaignSendsQuery($this->campaign, $request));

        return [
            'campaign' => $this->campaign,
            'sends' => $sendsQuery->paginate($request->per_page),
            'totalSends' => $this->campaign->sendsWithoutInvalidated()->count(),
            'totalPending' => $this->campaign->sends()->pending()->count(),
            'totalSent' => $this->campaign->sends()->sent()->count(),
            'totalFailed' => $this->campaign->sends()->failed()->count(),
            'totalBounces' => $this->campaign->sends()->bounced()->count(),
            'totalComplaints' => $this->campaign->sends()->complained()->count(),
        ];
    }
}
