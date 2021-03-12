<x-mailcoach::fieldset :focus="$editing" class="max-w-full">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold bg-yellow-200 text-yellow-600">
                {{ $index + 1 }}
            </span>
            <span class="font-normal whitespace-nowrap">
                Split
            </span>
        </header>
    </x-slot>

    <div class="flex items-center absolute top-4 right-6 space-x-3 z-20">
        @if ($editing && count($editingActions) === 0)
            <button type="button" wire:click="save">
                <i class="icon-button hover:text-green-500 fas fa-check"></i>
            </button>
        @elseif ($editable && !$editing)
            <button type="button" wire:click="edit">
                <i class="icon-button far fa-edit"></i>
            </button>
        @endif
        @if ($deletable)
            <button type="button" onclick="confirm('{{ __('Are you sure you want to delete this action?') }}') || event.stopImmediatePropagation()" wire:click="delete">
                <i class="icon-button hover:text-red-500 far fa-trash-alt"></i>
            </button>
        @endif
    </div>

        <div class="grid gap-6">
            @if ($editing)
                <div class="grid grid-cols-2 gap-6 w-full">
                    <section class="border-l-4 border-blue-400 bg-white bg-opacity-50">
                        <div class="grid gap-4 px-12 pb-8 border-blue-500 border-opacity-20 border-r border-t border-b rounded-r">
                            <livewire:automation-builder name="{{ $uuid }}-left-actions" :automation="$automation" :actions="$leftActions" key="{{ $uuid }}-left-actions" />
                        </div>
                    </section>
                    <section class="border-l-4 border-blue-400 bg-white bg-opacity-50">
                        <div class="grid gap-4 px-12 pb-8 border-blue-500 border-opacity-20 border-r border-t border-b rounded-r">
                            <livewire:automation-builder name="{{ $uuid }}-right-actions" :automation="$automation" :actions="$rightActions" key="{{ $uuid}}-right-actions" />
                        </div>
                    </section>
                </div>
            @else
                <div class="grid gap-6 flex-grow">
                    <div class="grid grid-cols-2 gap-6 w-full">
                        <section class="border-l-4 border-blue-400 bg-white bg-opacity-50">
                            <div class="grid gap-4 px-12 py-8 border-blue-500 border-opacity-20 border-r border-t border-b rounded-r">
                                @foreach ($leftActions as $index => $action)
                                    @livewire($action['class']::getComponent() ?: 'automation-action', array_merge([
                                        'index' => $index,
                                        'uuid' => $action['uuid'],
                                        'action' => $action,
                                        'automation' => $automation,
                                        'editable' => false,
                                        'deletable' => false,
                                    ], ($action['data'] ?? [])), key('left' . $index . $action['uuid']))
                                @endforeach
                            </div>
                        </section>
                        <section class="border-l-4 border-blue-400 bg-white bg-opacity-50">
                            <div class="grid gap-4 px-12 py-8 border-blue-500 border-opacity-20 border-r border-t border-b rounded-r">
                                @foreach ($rightActions as $index => $action)
                                    @livewire($action['class']::getComponent() ?: 'automation-action', array_merge([
                                        'index' => $index,
                                        'uuid' => $action['uuid'],
                                        'action' => $action,
                                        'automation' => $automation,
                                        'editable' => false,
                                        'deletable' => false,
                                    ], ($action['data'] ?? [])), key('right' . $index . $action['uuid']))
                                @endforeach
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
</x-mailcoach::fieldset>

