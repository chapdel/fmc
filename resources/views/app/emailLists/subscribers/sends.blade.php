@php($sends ??= null)
@php($totalSendsCount ??= null)
<x-mailcoach::data-table
    name="send"
    :rows="$sends"
    :totalRowsCount="$totalSendsCount"
    :columns="[
        ['label' => __('mailcoach - Campaign')],
        ['label' => __('mailcoach - Opens'), 'class' => 'w-32 th-numeric'],
        ['label' => __('mailcoach - Clicks'), 'class' => 'w-32 th-numeric'],
        ['label' => __('mailcoach - Sent'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
    rowPartial="mailcoach::app.emailLists.subscribers.partials.sendRow"
    :emptyText="__('mailcoach - This user hasn\'t received any campaign yet.')"
/>
