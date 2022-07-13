<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="legend">
        {{__('mailcoach - Add tags') }}
        <span class="form-legend-accent">
            {{ $tags }}
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12 md:col-span-6">
            <x-mailcoach::text-field
                id="tags"
                :label="__('mailcoach - Tags to add')"
                :required="true"
                name="tags"
                wire:model="tags"
            />
        </div>
    </x-slot>

</x-mailcoach::automation-action>
