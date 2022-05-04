<x-mailcoach::data-table
    name="list"
    :modelClass="\Spatie\Mailcoach\Domain\Shared\Support\Config::getEmailListClass()"
    :rows="$emailLists ?? null"
    :totalRowsCount="$totalEmailListsCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => '-active_subscribers_count', 'label' => __('mailcoach - Active'), 'class' => 'w-32 th-numeric'],
        ['attribute' => '-created_at', 'label' => __('mailcoach - Created'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.emailLists.partials.row"
    :emptyText="__('mailcoach - You\'ll need at least one list to gather subscribers.')"
/>
