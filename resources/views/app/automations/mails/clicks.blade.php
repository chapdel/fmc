<div>
    @if($mail->track_clicks)
        @php(($links ?? collect())->map(fn ($link) => $link->setRelation('automationMail', $mail)))
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
            rowPartial="mailcoach::app.automations.mails.partials.clickRow"
            :emptyText="__('mailcoach - No clicks yet. Stay tuned.')"
        />
    @else
        <x-mailcoach::info>
            {{ __('mailcoach - Click tracking was not enabled for this email.') }}
        </x-mailcoach::info>
    @endif
</div>
