<div>
    @if($campaign->click_count)
        <x-mailcoach::data-table
            name="clicks"
            :rows="$links ?? null"
            :totalRowsCount="$totalLinksCount ?? null"
            :columns="[
                ['attribute' => 'link', 'label' => __('mailcoach - Link')],
                ['label' => __('mailcoach - Tag')],
                ['attribute' => '-unique_click_count', 'label' => __('mailcoach - Unique Clicks'), 'class' => 'w-32 th-numeric hidden | xl:table-cell'],
                ['attribute' => '-click_count', 'label' => __('mailcoach - Clicks'), 'class' => 'w-32 th-numeric'],
            ]"
            rowPartial="mailcoach::app.campaigns.partials.clickRow"
            :rowData="[
                'campaign' => $campaign,
            ]"
            :emptyText="__('mailcoach - No clicks yet. Stay tuned.')"
        />
    @else
        <x-mailcoach::card>
            <x-mailcoach::info>
                {{ __('mailcoach - No clicks tracked') }}
            </x-mailcoach::info>
        </x-mailcoach::card>
    @endif
</div>
