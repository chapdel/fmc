<x-mailcoach::data-table
    name="automation"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getAutomationClass()"
    :rows="$automations ?? null"
    :totalRowsCount="$totalAutomationsCount ?? null"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => '-updated_at', 'label' => __('mailcoach - Last updated'), 'class' => 'w-48 th-numeric'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.automations.partials.row"
    :emptyText="__('mailcoach - No automations yet. A welcome automation is a good start!')"
    :noResultsText="__('mailcoach - No automations found.')"
/>
