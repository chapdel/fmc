<div>
    <div class="mb-4">
        <x-mailcoach::text-field
            :label="__('Duration to check for')"
            name="checkFor"
            wire:model="checkFor"
            :value="$checkFor"
        />
    </div>

    <div class="flex flex-wrap">
        @foreach ($tags as $index => $tag)
            <fieldset class="w-1/3 border-2 border-blue-100 p-2">
                <livewire:tag-chain :automation="$automation" :initial="$tag" :key="$index" :index="$index" />
            </fieldset>
        @endforeach
        <div class="w-full mt-4">
            <button type="button" class="button" wire:click.prevent="addTag()">
                <x-mailcoach::icon-label icon="fa-tag" :text="__('Add tag check')" />
            </button>
        </div>
    </div>
    <div class="mt-4">
        <h2 class="font-bold mb-2">Default actions</h2>
        <livewire:automation-builder name="default-actions" :automation="$automation" :actionData="['actions' => $defaultActions]" />
    </div>
</div>
