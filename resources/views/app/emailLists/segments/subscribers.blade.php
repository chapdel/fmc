@php($subscribers ??= null)
@php($selectedSubscribersCount ??= null)
@php($subscribersCount ??= null)
<x-mailcoach::data-table
    name="subscriber"
    :rows="$subscribers"
    :totalRowsCount="$selectedSubscribersCount"
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
>
    @slot('actions')
        @if(!is_null($selectedSubscribersCount) && !is_null($subscribersCount))
            <div class="alert alert-info mb-8">
                {!! __('mailcoach - Population is <strong>:percentage%</strong> of list total of :subscribersCount.', ['percentage' => round($selectedSubscribersCount / $subscribersCount * 100 , 2), 'subscribersCount' => number_format($subscribersCount)]) !!}
            </div>
        @endif
    @endslot
</x-mailcoach::data-table>
