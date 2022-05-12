<div>
    <div class="flex items-center">
        <x-mailcoach::date-field
            :name="$name . '[date]'"
            wire:change="$set('{{ $name }}.date', $event.target.value)"
            :value="$value->format('Y-m-d')"
            required
        />
        <span class="mx-3">at</span>
        <x-mailcoach::select-field
            :name="$name . '[hours]'"
            wire:change="$set('{{ $name }}.hours', $event.target.value)"
            :options="$hourOptions"
            :value="$value->format('H')"
            required
        />
        <span class="mx-1">:</span>
        <x-mailcoach::select-field
            :name="$name . '[minutes]'"
            wire:change="$set('{{ $name }}.minutes', $event.target.value)"
            :options="$minuteOptions"
            :value="$value->format('i')"
            required
        />
    </div>
    @error($name)
        <p class="form-error mb-1" role="alert">{{ $message }}</p>
    @enderror
</div>
