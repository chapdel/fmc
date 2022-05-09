<x-mailcoach::data-table
    name="template"
    :modelClass="\Spatie\Mailcoach\Domain\Shared\Support\Config::getTemplateClass()"
    :rows="$templates ?? null"
    :totalRowsCount="$totalTemplatesCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => 'updated_at', 'label' => __('mailcoach - Last updated'), 'class' => 'w-48 th-numeric'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.campaigns.templates.partials.row"
    :emptyText="
        ($this->filter['search'] ?? null)
            ? __('mailcoach - No templates found.')
            : __('mailcoach - DRY? No templates here.')
    "
/>
