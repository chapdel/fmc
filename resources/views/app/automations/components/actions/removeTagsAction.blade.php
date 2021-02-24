<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="form">
        <x-mailcoach::text-field
            id="tags"
            :label="__('Tags to remove')"
            :required="true"
            name="tags"
            wire:model="tags"
        />
    </x-slot>

    <x-slot name="content">
        <div class="tag-neutral">
            {{ $tags }}
        </div>
    </x-slot>
</x-mailcoach::automation-action>
