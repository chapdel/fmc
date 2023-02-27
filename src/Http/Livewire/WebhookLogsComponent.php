<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\WebhookLogsQuery;

class WebhookLogsComponent extends DataTableComponent
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public string $sort = '-created_at';

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
            'webhookLogs' => (new WebhookLogsQuery($request))->paginate(),
            'totalWebhookLogsCount' => self::getWebhookLogClass()::count(),
        ];
    }
}
