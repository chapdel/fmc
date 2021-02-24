<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable" :deletable="$deletable">
    <x-slot name="form">
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
                    <div class="relative">
                        <button class="absolute top-0 right-0 z-20" type="button" wire:click="removeTag({{ $index }})">
                            <i class="icon-button hover:text-red-500 far fa-trash-alt"></i>
                        </button>
                        <livewire:tag-chain
                            :automation="$automation"
                            :key="\Illuminate\Support\Str::random()"
                            :tag="$tag['tag'] ?? ''"
                            :actions="$tag['actions'] ?? []"
                            :index="$index"
                        />
                    </div>
                @endforeach
            </div>
            <div class="w-full mt-4">
                <x-mailcoach::button :label="__('Add tag check')" wire:click.prevent="addTag()" />
            </div>
            <div class="mt-6">
                <h2 class="font-bold mb-2">Default actions</h2>
                <livewire:automation-builder name="default-actions" :automation="$automation" :actions="$defaultActions" :key="\Illuminate\Support\Str::random()" />
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="tag-neutral">
            <div class="grid grid-cols-2">
                <section class="p-6">
                    <h4 class="mb-4 markup-h4">Checking following tags for {{ $checkFor }}</h4>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($tags as $tag)
                            <div class="grid justify-items-start gap-2">
                                <div class=tag>{{ $tag['tag'] }}</div>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach ($tag['actions'] as $index => $action)
                                        @livewire($action['class']::getComponent(), array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key(\Illuminate\Support\Str::random()))
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                <section class="p-6 border-l border-gray-300">
                    <h4 class="mb-4 markup-h4"><strong>Default</strong> when no tag matches</h4>
                    <div class="grid justify-items-start gap-2">
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($defaultActions as $index => $action)
                                @livewire($action['class']::getComponent(), array_merge([
                                    'index' => $index,
                                    'uuid' => $action['uuid'],
                                    'action' => $action,
                                    'automation' => $automation,
                                    'editable' => false,
                                    'deletable' => false,
                                ], ($action['data'] ?? [])), key(\Illuminate\Support\Str::random()))
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </x-slot>
</x-mailcoach::automation-action>
