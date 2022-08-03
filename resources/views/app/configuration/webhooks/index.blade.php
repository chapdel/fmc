<x-mailcoach::data-table
    name="webhook"
    :modelClass="config('mailcoach.models.webhook_configuration', \Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration::class)"
    :rows="$webhooks ?? null"
    :totalRowsCount="$totalWebhooksCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __('Name'), 'class' => 'w-64'],
        ['attribute' => 'url', 'label' => __('URL'), 'class' => 'w-64'],
        ['attribute' => 'use_for_all_lists', 'label' => __('Use for all lists'), 'class' => 'w-48'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.configuration.webhooks.partials.row"
    :emptyText="__('No webhooks configurations. You can use webhooks to get notified immediately when certain events (such as subscriptions) occur.')"
/>

