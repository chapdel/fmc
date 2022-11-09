<x-mailcoach::data-table
    name="mailer"
    :modelClass="config('mailcoach-ui.models.mailer', \Spatie\Mailcoach\Domain\Settings\Models\Mailer::class)"
    :rows="$mailers ?? null"
    :totalRowsCount="$totalMailersCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __('Name'), 'class' => 'w-64'],
        ['attribute' => 'transport', 'label' => __('Transport'), 'class' => 'w-48'],
        ['attribute' => 'ready_for_use', 'label' => __('Ready for use'), 'class' => 'w-48'],
        ['attribute' => 'default', 'label' => __('Default'), 'class' => 'w-48'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.configuration.mailers.partials.row"
    :emptyText="__('No mailers')"
/>
