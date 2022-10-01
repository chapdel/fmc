<x-mailcoach::data-table
    :query="Post::query()"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => '-active_subscribers_count', 'label' => __mc('Active'), 'class' => 'w-32 th-numeric'],
        ['attribute' => '-created_at', 'label' => __mc('Created'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
        Column::new($name)->label('sdmlfskjdfm')->initialSort()->class('w-48')->notSortable()->notSearchable(),
        Column::new()->class('w-12'),
    ]"
    rowPartial="mailcoach::app.emailLists.partials.row"
    :emptyText="
        ($this->filter['search'] ?? null)
            ? __mc('No email lists found.')
            : __mc('You\'ll need at least one list to gather subscribers.')
    "
>
    <slot name="row" $row>
        {{ $row }}
        @include('smdklqjsmdf')
    </slot>
    <slot name="loading">

    </slot>
    <slot name="noResults">

    </slot>
    <slot name="header">
        $columns
    </slot>

</x-mailcoach::data-table>
