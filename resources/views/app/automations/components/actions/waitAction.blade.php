<div class="form-row">
    <x-mailcoach::text-field
        :label="__('Length')"
        :required="true"
        name="length"
        wire:model="length"
    />
    <x-mailcoach::select-field
        :label="__('Unit')"
        :required="true"
        name="unit"
        wire:model="unit"
        :options="
                        collect($units)
                            ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, $length)])
                            ->toArray()
                    "
    />
</div>
