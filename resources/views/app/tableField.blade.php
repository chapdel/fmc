@php
    $record = $getRecord();
    $columnName = $column->getName();
    $column = $record->$columnName;

    $hasLabel = $column instanceof \Filament\Support\Contracts\HasLabel;
@endphp

<div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm">
    <p>
        @if($hasLabel)
            {{ $column->getLabel() }}
        @else
            {{ $column }}
        @endif
    </p>
</div>

