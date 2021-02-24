<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="form">
        <x-mailcoach::text-field
            :label="__('Length')"
            :required="true"
            name="length"
            wire:model="length"
            type="number"
        />
        <x-mailcoach::select-field
            :label="__('Unit')"
            :required="true"
            name="unit"
            wire:model="unit"
            :options="
                collect($units)
                    ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, (int) $length)])
                    ->toArray()
            "
        />
    </x-slot>

    <x-slot name="content">
        <div class="tag-neutral">
            @if ($length && $unit && $interval = \Carbon\CarbonInterval::createFromDateString("{$length} {$unit}"))
                {!! $interval->cascade()->forHumans() !!}
            @endif
        </div>
    </x-slot>
</x-mailcoach::automation-action>
