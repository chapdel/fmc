<div>
    @if($campaign->track_clicks)
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
            />
        @else
            <x-mailcoach::help>
                {{ __('mailcoach - No clicks yet. Stay tuned.') }}
            </x-mailcoach::help>
        @endif
    @else
        <x-mailcoach::help>
            {{ __('mailcoach - Click tracking was not enabled for this campaign.') }}
        </x-mailcoach::help>
    @endif
</div>
