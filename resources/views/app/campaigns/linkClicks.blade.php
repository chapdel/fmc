<div>
    <x-mailcoach::data-table
        name="link-clicks"
        :rows="$linkClicks ?? null"
        :totalRowsCount="$totalLinkClicksCount ?? null"
        :columns="[
            ['attribute' => 'email', 'label' => __mc('Email')],
            ['attribute' => 'click_count', 'label' => __mc('Opens'), 'class' => 'w-32 th-numeric'],
            ['attribute' => '-first_clicked_at', 'label' => __mc('First clicked at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ]"
        rowPartial="mailcoach::app.campaigns.partials.linkClickRow"
        :emptyText="__mc('No clicks yet. Stay tuned.')"
    />
</div>
