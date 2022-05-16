@if($this->filter['search'] ?? '')
    @php($emptyText = __('mailcoach - No campaigns found.'))
@elseif ($totalListsCount ?? 0)
    @php($emptyText = __('mailcoach - No campaigns yet. Go write something!'))
@else
    @php($emptyText = __('mailcoach - No campaigns yet, but you‘ll need a list first, go <a href=":emailListsLink">create one</a>!', ['emailListsLink' => route('mailcoach.emailLists')]))
@endif
<x-mailcoach::data-table
    name="campaign"
    :rows="$campaigns ?? null"
    :totalRowsCount="$totalCampaignsCount ?? null"
    :filters="[
        ['attribute' => 'status', 'value' => '', 'label' => __('mailcoach - All'), 'count' => $totalCampaignsCount ?? null],
        ['attribute' => 'status', 'value' => 'sent', 'label' => __('mailcoach - Sent'), 'count' => $sentCampaignsCount ?? null],
        ['attribute' => 'status', 'value' => 'scheduled', 'label' => __('mailcoach - Scheduled'), 'count' => $scheduledCampaignsCount ?? null],
        ['attribute' => 'status', 'value' => 'draft', 'label' => __('mailcoach - Draft'), 'count' => $draftCampaignsCount ?? null],
    ]"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => 'email_list_id', 'label' => __('mailcoach - List'), 'class' => 'w-48'],
        ['attribute' => '-sent_to_number_of_subscribers', 'label' => __('mailcoach - Emails'), 'class' => 'w-24 th-numeric'],
        ['attribute' => '-unique_open_count', 'label' => __('mailcoach - Opens'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-unique_click_count', 'label' => __('mailcoach - Clicks'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-sent', 'label' => __('mailcoach - Sent'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.campaigns.partials.row"
    :emptyText="$emptyText"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Mailcoach::getCampaignClass())
            @if ($totalListsCount ?? 0)
                <x-mailcoach::button x-on:click="$store.modals.open('create-campaign')"
                                     :label="__('mailcoach - Create campaign')"/>
            @endif

            <x-mailcoach::modal name="create-campaign" :title="__('mailcoach - Create campaign')"
                                :confirm-text="__('mailcoach - Create campaign')">
                <livewire:mailcoach::create-campaign/>
            </x-mailcoach::modal>
        @endcan
    @endslot
</x-mailcoach::data-table>
