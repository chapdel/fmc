<div>
    <div class="mb-4">
        <x-mailcoach::text-field
            :label="__('Duration to check for')"
            name="checkFor"
            wire:model="checkFor"
            :value="$checkFor"
        />
    </div>

    <div class="grid grid-cols-2">
        @foreach ($tags as $index => $tag)
            <x-mailcoach::fieldset>
                <livewire:tag-chain :automation="$automation" :initial="$tag" :key="$index" :index="$index" />
            </x-mailcoach::fieldset>
        @endforeach
    </div>
    <div class="w-full mt-4">
        <x-mailcoach::button :label="__('Add tag check')" wire:click.prevent="addTag()" />
    </div>
    <div class="mt-6">
        <h2 class="font-bold mb-2">Default actions</h2>
        <livewire:automation-builder name="default-actions" :automation="$automation" :componentData="['actions' => $defaultActions]" />
    </div>
</div>
