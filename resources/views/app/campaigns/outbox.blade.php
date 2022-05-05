<x-mailcoach::data-table
    name="sends"
    :rows="$sends ?? null"
    :totalRowsCount="$totalSends ?? null"
    :filters="[
        ['attribute' => 'type', 'value' => '', 'label' => __('mailcoach - All'), 'count' => $totalSends ?? null],
        ['attribute' => 'type', 'value' => 'pending', 'label' => __('mailcoach - Pending'), 'count' => $totalPending ?? null],
        ['attribute' => 'type', 'value' => 'failed', 'label' => __('mailcoach - Failed'), 'count' => $totalFailed ?? null],
        ['attribute' => 'type', 'value' => 'sent', 'label' => __('mailcoach - Sent'), 'count' => $totalSent ?? null],
        ['attribute' => 'type', 'value' => 'bounced', 'label' => __('mailcoach - Bounced'), 'count' => $totalBounces ?? null],
        ['attribute' => 'type', 'value' => 'complained', 'label' => __('mailcoach - Complaints'), 'count' => $totalComplaints ?? null],
    ]"
    :columns="[
        ['attribute' => 'subscriber_email', 'label' => __('mailcoach - Email address')],
        ['attribute' => 'subscriber_email', 'label' => __('mailcoach - Problem')],
        ['attribute' => '-sent_at', 'label' => __('mailcoach - Sent at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
    rowPartial="mailcoach::app.campaigns.partials.outboxRow"
/>
