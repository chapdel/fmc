<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Campaign\Jobs\RetrySendingFailedSendsJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\CampaignSendsQuery;
use Spatie\Mailcoach\MainNavigation;
use Spatie\QueryBuilder\QueryBuilder;

class CampaignOutboxComponent extends DataTableComponent
{
    public string $sort = '-sent_at';

    protected array $allowedFilters = [
        'type' => ['except' => ''],
    ];

    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        app(MainNavigation::class)->activeSection()?->add($this->campaign->name, route('mailcoach.campaigns'));
    }

    public function retryFailedSends()
    {
        $this->authorize('update', $this->campaign);

        $failedSendsCount = $this->campaign->sends()->failed()->count();

        if ($failedSendsCount === 0) {
            $this->flash(__mc('There are not failed mails to resend anymore.'), 'error');

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

    public function getView(): string
    {
        return 'mailcoach::app.campaigns.outbox';
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

    /**
     * @param  Send  $model
     */
    public function formatExportRow(Model $model): array
    {
        return [
            'email' => $model->subscriber->email,
            'problem' => $model->failure_reason.optional($model->latestFeedback())->formatted_type,
            'sent_at' => optional($model->sent_at)->toMailcoachFormat() ?? '-',
        ];
    }

    public function getQuery(Request $request): QueryBuilder
    {
        return new CampaignSendsQuery($this->campaign, $request);
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->campaign);

        $sendsQuery = $this->getQuery($request);

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
