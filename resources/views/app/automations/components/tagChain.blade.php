<div>
    <div class="mb-4">
        <x-mailcoach::text-field
            :label="__('Tag')"
            name="tag"
            wire:model="tag"
        />
    </div>

    <livewire:automation-builder :automation="$automation" :componentData="['actions' => $actions]" />
</div>
