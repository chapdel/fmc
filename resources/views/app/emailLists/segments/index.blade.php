<x-mailcoach::data-table
    name="segment"
    :rows="$segments ?? null"
    :totalRowsCount="$totalSegmentsCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => '-created_at', 'label' => __('mailcoach - Created at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.emailLists.segments.partials.row"
    :emptyText="__('mailcoach - No segments here. So you don\'t like putting people into groups?')"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Mailcoach::getTagSegmentClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-segment')"
                                 :label="__('mailcoach - Create segment')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create segment')" name="create-segment">
                @livewire('mailcoach::create-segment', [
                    'emailList' => $emailList,
                ])
            </x-mailcoach::modal>
        @endcan
    @endslot
</x-mailcoach::data-table>
