@php($suppressions ??= null)
@php($totalSuppressionsCount ??= null)
<x-mailcoach::data-table
    name="suppression"
    :rows="$suppressions"
    :totalRowsCount="$totalSuppressionsCount"
    :columns="[
        ['attribute' => 'email', 'label' => __mc('Email')],
        ['attribute' => 'stream', 'label' => __mc('Type'), 'class' => 'w-48'],
        ['attribute' => 'reason', 'label' => __mc('Reason')],
        ['class' => 'w-48'],
    ]"
    rowPartial="livewire.suppressionRow"
    :emptyText="__mc('No suppressions found.')"
>
</x-mailcoach::data-table>
