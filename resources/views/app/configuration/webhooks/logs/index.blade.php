<x-mailcoach::data-table
    name="webhook"
    :modelClass="config('mailcoach.models.webhook_log', \Spatie\Mailcoach\Domain\Settings\Models\WebhookLog::class)"
    :rows="$webhookLogs ?? null"
    :totalRowsCount="$totalWebhookLogsCount ?? null"
    :columns="[
        ['attribute' => 'status_code', 'label' => __mc('Status Code'), 'class' => 'w-48'],
        ['attribute' => 'event_type', 'label' => __mc('Event Type'), 'class' => 'w-64'],
        ['attribute' => 'attempt', 'label' => __mc('Attempt #'), 'class' => 'w-48'],
        ['attribute' => 'created_at', 'label' => __mc('Sent at'), 'class' => 'w-64'],
        ['class' => 'w-48'],
    ]"
    rowPartial="mailcoach::app.configuration.webhooks.logs.partials.row"
    :emptyText="__mc('No webhooks logs.')"
/>
