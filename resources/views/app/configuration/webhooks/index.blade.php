<x-mailcoach::data-table
    name="webhook"
    :modelClass="config('mailcoach.models.webhook_configuration', \Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration::class)"
    :rows="$webhooks ?? null"
    :totalRowsCount="$totalWebhooksCount ?? null"
    :columns="$this->getColumns()"
    rowPartial="mailcoach::app.configuration.webhooks.partials.row"
    :emptyText="__mc('No webhooks configurations. You can use webhooks to get notified immediately when certain events (such as subscriptions) occur.')"
/>
