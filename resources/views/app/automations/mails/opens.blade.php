<div>
    @if($mail->open_count)
        <x-mailcoach::data-table
            name="open"
            :rows="$mailOpens ?? null"
            :totalRowsCount="$totalMailOpensCount ?? null"
            :columns="[
                ['attribute' => 'email', 'label' => __('mailcoach - Email')],
                ['attribute' => 'open_count', 'label' => __('mailcoach - Opens'), 'class' => 'w-32 th-numeric'],
                ['attribute' => '-first_opened_at', 'label' => __('mailcoach - First opened at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
            ]"
            rowPartial="mailcoach::app.automations.mails.partials.openRow"
            :emptyText="__('mailcoach - No opens yet. Stay tuned.')"
        />
    @else
        <x-mailcoach::help>
            {{ __('mailcoach - No opens tracked') }}
        </x-mailcoach::help>
    @endif
</div>
