<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Settings\Actions\ResendWebhookCallAction;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\WebhookLogsQuery;
use Spatie\Mailcoach\Mailcoach;

class WebhookLogsComponent extends DataTableComponent
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public string $sort = '-created_at';

    public WebhookConfiguration $webhook;

    public function getTitle(): string
    {
        return __mc('Webhook Logs');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.webhooks.logs.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Webhook Logs'),
        ];
    }

    public function getData(Request $request): array
    {
        return [
            'webhookLogs' => (new WebhookLogsQuery($this->webhook, $request))->paginate(),
            'totalWebhookLogsCount' => self::getWebhookLogClass()::count(),
        ];
    }

    public function resend(WebhookLog $webhookLog)
    {
        $this->resendWebhookAction()->execute($webhookLog);

        return redirect()->route('webhooks.logs.index', [
            'webhook' => $webhookLog->webhookConfiguration,
        ]);
    }

    protected function resendWebhookAction(): ResendWebhookCallAction
    {
        /** @var $action ResendWebhookCallAction */
        $action = Mailcoach::getSharedActionClass('resend_webhook', ResendWebhookCallAction::class);

        return $action;
    }
}
