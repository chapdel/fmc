<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\QueryBuilder;

class WebhookLogsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(?Request $request = null)
    {
        ray('WebhookLogsQuery');
        parent::__construct(self::getWebhookLogClass()::query(), $request);

        $this->defaultSort(['created_at', 'desc'])
            ->allowedSorts('created_at');
    }
}
