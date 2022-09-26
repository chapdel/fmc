@php($tags ??= null)
@php($totalTagsCount ??= null)
<x-mailcoach::data-table
    name="tag"
    :rows="$tags"
    :totalRowsCount="$totalTagsCount"
    :filters="[
        ['attribute' => 'type', 'value' => '', 'label' => __('mailcoach - All'), 'count' => $totalTagsCount ?? null],
        ['attribute' => 'type', 'value' => \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default, 'label' => __('mailcoach - Default'), 'count' => $totalDefault ?? null],
        ['attribute' => 'type', 'value' => \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Mailcoach, 'label' => __('mailcoach - Mailcoach'), 'count' => $totalMailcoach ?? null],
    ]"
    :columns="[
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => 'visible_in_preferences', 'label' => __('mailcoach - Visible')],
        ['attribute' => 'subscriber_count', 'label' => __('mailcoach - Subscribers'), 'class' => 'w-32 th-numeric'],
        ['attribute' => 'updated_at', 'label' => __('mailcoach - Updated at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.emailLists.tags.partials.row"
    :rowData="['emailList' => $emailList]"
    :emptyText="__('mailcoach - There are no tags for this list. Everyone is equal!')"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Mailcoach::getTagClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-tag')" :label="__('mailcoach - Create tag')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create tag')" name="create-tag">
                @livewire('mailcoach::create-tag', [
                    'emailList' => $emailList,
                ])
            </x-mailcoach::modal>
        @endcan
    @endslot
</x-mailcoach::data-table>
