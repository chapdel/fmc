<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\QueryBuilder;

class WebhookLogsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(WebhookConfiguration $webhookConfiguration, Request $request = null)
    {
        parent::__construct(self::getWebhookLogClass()::query(), $request);

        $this->where('webhook_configuration_id', $webhookConfiguration->id)
            ->defaultSort(['created_at', 'desc'])
            ->allowedSorts([
                'created_at',
                'status_code',
                'event_type',
                'attempt',
            ])->allowedFilters([]);
    }
}
