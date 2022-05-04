<x-mailcoach::data-table
    name="automationMail"
    :rows="$automationMails"
    :totalRowsCount="$totalAutomationMailsCount"
    :columns="[
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => '-sent_to_number_of_subscribers', 'label' => __('mailcoach - Emails'), 'class' => 'w-24 th-numeric'],
        ['attribute' => '-unique_open_count', 'label' => __('mailcoach - Opens'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-unique_click_count', 'label' => __('mailcoach - Clicks'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-created_at', 'class' => 'w-48 th-numeric hidden | xl:table-cell', 'label' => __('mailcoach - Created at')],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.automations.mails.partials.row"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Domain\Shared\Support\Config::getAutomationMailClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-automation-mail')" :label="__('mailcoach - Create email')"/>

            <x-mailcoach::modal :title="__('mailcoach - Create email')" name="create-automation-mail" :open="$errors->any()">
                <livewire:mailcoach::create-automation-mail />
            </x-mailcoach::modal>
        @endcan
    @endslot
</x-mailcoach::data-table>
