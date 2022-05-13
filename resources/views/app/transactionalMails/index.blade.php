<x-mailcoach::data-table
    name="transactional-mail"
    :rows="$transactionalMails ?? null"
    :totalRowsCount="$transactionalMailsCount ?? null"
    :columns="[
        ['attribute' => 'subject', 'label' => __('mailcoach - Subject')],
        ['label' => __('mailcoach - To')],
        ['label' => __('mailcoach - Opens'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['label' => __('mailcoach - Clicks'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-created_at', 'label' => __('mailcoach - Sent'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.transactionalMails.row"
    :emptyText="__('mailcoach - No transactional mails have been sent yet!')"
/>
