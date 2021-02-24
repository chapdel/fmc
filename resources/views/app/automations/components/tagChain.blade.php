<div>
    <div class="mb-4">
        <x-mailcoach::text-field
            :label="__('Tag')"
            :name="'tags.' . $index . '.tag'"
            wire:model="tag"
        />
    </div>

    <livewire:automation-builder :name="'tags.' . $index . '.actions'" :automation="$automation" :actions="$actions" />
</div>
