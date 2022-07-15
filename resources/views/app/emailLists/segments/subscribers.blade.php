<div class="card-grid">
<x-mailcoach::card class="py-4">
    <x-mailcoach::info>
        @if(($selectedSubscribersCount ?? null) && ($subscribersCount ?? null))
            {!! __('mailcoach - Population is <strong>:percentage%</strong> of list total of :subscribersCount.', ['percentage' => round($selectedSubscribersCount / $subscribersCount * 100 , 2), 'subscribersCount' => number_format($subscribersCount)]) !!}
        @else
            {{ __('mailcoach - Loading') }}...
        @endif
    </x-mailcoach::info>
</x-mailcoach::card>
<x-mailcoach::data-table
    name="subscriber"
    :rows="$subscribers ?? null"
    :totalRowsCount="$selectedSubscribersCount ?? null"
    :columns="[
        ['attribute' => 'email', 'label' => __('mailcoach - Email')],
        ['label' => __('mailcoach - Tags')],
    ]"
    rowPartial="mailcoach::app.emailLists.segments.partials.subscriberRow"
    :rowData="[
        'emailList' => $emailList,
    ]"
    :emptyText="__('mailcoach - This is a very exclusive segment. Nobody got selected.')"
    :searchable="false"
/>
</div>
