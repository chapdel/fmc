<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\MailersQuery;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\WebhooksQuery;

class Webhooks extends DataTable
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public function getTitle(): string
    {
        return __('Webhooks');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.webhooks.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __('Webhooks'),
        ];
    }

    public function deleteWebhook(int $id)
    {
        $webhook = self::getWebhookConfigurationClass()::find($id);

        $webhook->delete();

        $this->flash(__('Webhook :webhook successfully deleted', ['webhook' => $webhook->name]));
    }

    public function getData(Request $request): array
    {
        return [
            'webhooks' => (new WebhooksQuery($request))->paginate(),
            'totalWebhooksCount' => self::getWebhookConfigurationClass()::count(),
        ];
    }
}
