<x-mailcoach::data-table
    name="unsubscribe"
    :rows="$unsubscribes ?? null"
    :totalRowsCount="$totalUnsubscribes ?? null"
    rowPartial="mailcoach::app.automations.mails.partials.unsubscribeRow"
    :emptyText="__('mailcoach - No unsubscribes have been received yet.')"
    :columns="[
        ['label' => __('mailcoach - Email')],
        ['attribute' => '-created_at', 'label' => __('mailcoach - Date'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
/>
