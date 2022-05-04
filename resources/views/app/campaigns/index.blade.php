<x-mailcoach::data-table
    name="campaign"
    :rows="$campaigns"
    :totalRowsCount="$totalCampaignsCount"
    :filters="[
        ['attribute' => 'status', 'value' => '', 'label' => __('mailcoach - All'), 'count' => $totalCampaignsCount],
        ['attribute' => 'status', 'value' => 'sent', 'label' => __('mailcoach - Sent'), 'count' => $sentCampaignsCount],
        ['attribute' => 'status', 'value' => 'scheduled', 'label' => __('mailcoach - Scheduled'), 'count' => $scheduledCampaignsCount],
        ['attribute' => 'status', 'value' => 'draft', 'label' => __('mailcoach - Draft'), 'count' => $draftCampaignsCount],
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
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Domain\Shared\Support\Config::getCampaignClass())
            @if ($totalListsCount || $totalCampaignsCount)
                <x-mailcoach::button x-on:click="$store.modals.open('create-campaign')" :label="__('mailcoach - Create campaign')" />

                <x-mailcoach::modal name="create-campaign" :title="__('mailcoach - Create campaign')" :confirm-text="__('mailcoach - Create campaign')">
                    <livewire:mailcoach::create-campaign />
                </x-mailcoach::modal>
            @endif
        @endcan
    @endslot

    @slot('empty')
        <x-mailcoach::help>
            @if($this->filter['search'])
                {{ __('mailcoach - No campaigns found.') }}
            @elseif ($totalListsCount)
                {{ __('mailcoach - No campaigns yet. Go write something!') }}
            @else
                {!! __('mailcoach - No campaigns yet, but you‘ll need a list first, go <a href=":emailListsLink">create one</a>!', ['emailListsLink' => route('mailcoach.emailLists')]) !!}
            @endif
        </x-mailcoach::help>
    @endslot
</x-mailcoach::data-table>
