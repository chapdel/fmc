@php($tags ??= null)
@php($totalTagsCount ??= null)
<x-mailcoach::data-table
    name="tag"
    :rows="$tags"
    :totalRowsCount="$totalTagsCount"
    :filters="[
        ['attribute' => 'type', 'value' => '', 'label' => __('mailcoach - All'), 'count' => $totalTagsCount ?? null],
        ['attribute' => 'type', 'value' => \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::DEFAULT, 'label' => __('mailcoach - Default'), 'count' => $totalDefault ?? null],
        ['attribute' => 'type', 'value' => \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::MAILCOACH, 'label' => __('mailcoach - Mailcoach'), 'count' => $totalMailcoach ?? null],
    ]"
    :columns="[
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => 'subscriber_count', 'label' => __('mailcoach - Subscribers'), 'class' => 'w-32 th-numeric'],
        ['attribute' => 'updated_at', 'label' => __('mailcoach - Updated at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
    rowPartial="mailcoach::app.emailLists.tags.partials.row"
    :rowData="['emailList' => $emailList]"
    :emptyText="__('mailcoach - There are no tags for this list. Everyone equal!')"
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
