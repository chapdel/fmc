<x-mailcoach::data-table
    name="transactional-template"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getTransactionalMailTemplateClass()"
    :rows="$templates ?? null"
    :totalRowsCount="$templatesCount ?? null"
    :columns="[
        ['attribute' => 'subject', 'label' => __('mailcoach - Name')],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.transactionalMails.templates.partials.row"
    :emptyText="__('mailcoach - You have not created any templates yet.')"
/>
