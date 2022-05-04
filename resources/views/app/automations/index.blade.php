<x-mailcoach::data-table
    name="automation"
    :modelClass="\Spatie\Mailcoach\Domain\Shared\Support\Config::getAutomationClass()"
    :rows="$automations ?? null"
    :totalRowsCount="$totalAutomationsCount ?? null"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'name', 'label' => __('mailcoach - Name')],
        ['attribute' => '-updated_at', 'label' => __('mailcoach - Last updated'), 'class' => 'w-48 th-numeric'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.automations.partials.row"
/>
