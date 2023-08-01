<x-mailcoach::condition :index="$index" :title="$title">
    <div wire:key="campaign-{{ $campaignId }}-options" class="grid">
        <div class="col-span-4">
            <x-mailcoach::select-field
                :label="__mc('Campaign')"
                name="campaign-{{ $index }}"
                :options="$campaigns"
                :sort="false"
                wire:model="campaignId"
                required
            />
        </div>
        <div class="col-span-4">
            <x-mailcoach::select-field
                :label="__mc('Comparison')"
                name="operator-{{ $index }}"
                wire:model="storedCondition.comparison_operator"
                :options="$storedCondition['condition']['comparison_operators'] ?? []"
                :sort="false"
                required
            />
        </div>
        @if(!in_array($storedCondition['comparison_operator'], ['any', 'none']))
            <div class="col-span-4">
                <x-mailcoach::select-field
                    :label="__mc('Value')"
                    name="value-{{ $index }}"
                    :options="$options"
                    wire:model="storedCondition.value"
                    required
                />
            </div>
        @endif
    </div>
</x-mailcoach::condition>
