<div>
    @if($campaign->track_opens)
        @if($campaign->open_count)
            <x-mailcoach::data-table
                name="open"
                :rows="$campaignOpens ?? null"
                :totalRowsCount="$totalMailOpensCount ?? null"
                :columns="[
                    ['attribute' => 'email', 'label' => __('mailcoach - Email')],
                    ['attribute' => 'open_count', 'label' => __('mailcoach - Opens'), 'class' => 'w-32 th-numeric'],
                    ['attribute' => '-first_opened_at', 'label' => __('mailcoach - First opened at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
                ]"
                rowPartial="mailcoach::app.campaigns.partials.openRow"
            />
        @else
            <x-mailcoach::help>
                {{ __('mailcoach - No opens yet. Stay tuned.') }}
            </x-mailcoach::help>
        @endif
    @else
        <x-mailcoach::help>
            {{ __('mailcoach - Open tracking was not enabled for this campaign.') }}
        </x-mailcoach::help>
    @endif
</div>
