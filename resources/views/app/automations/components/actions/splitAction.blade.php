<x-mailcoach::fieldset clean :focus="$editing">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold automation-counter">
                {{ $index + 1 }}
            </span>
            <span class="font-normal whitespace-nowrap">
                {{ __('mailcoach - Branch out') }}
            </span>
        </header>
    </x-slot>

    <div class="flex items-center absolute top-4 right-6 gap-3 z-20">
        @if ($editing && count($editingActions) === 0)
            <button type="button" wire:click="save">
                <i class="icon-button hover:text-green-500 fas fa-check"></i>
            </button>
        @elseif ($editable && !$editing)
            <button type="button" wire:click="edit">
                <i class="icon-button far fa-edit"></i>
            </button>
        @endif
        @if ($deletable && count($editingActions) === 0)
            <button type="button" onclick="confirm('{{ __('mailcoach - Are you sure you want to delete this action?') }}') || event.stopImmediatePropagation()" wire:click="delete">
                <i class="icon-button text-red-500 hover:text-red-700 far fa-trash-alt"></i>
            </button>
        @endif
    </div>

        <div class="grid gap-6">
            @if ($editing)
                <div class="grid gap-6 w-full">
                    <section class="before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-blue-500 before:to-blue-500/70 before:rounded-l-md bg-white/50">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-gray-900/10 border-r border-t border-b rounded-r">
                            <div class="flex items-center">
                                <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-blue-500/10 text-white rounded-br space-x-2">
                                    <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                    <span class="font-normal">{{ __('mailcoach - Branch') }}</span>
                                    A
                                </span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($leftActions) }} {{ trans_choice('mailcoach - action|actions', count($leftActions)) }}</span>
                                <button class="ml-auto -mr-8 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-left-actions" :automation="$automation" :actions="$leftActions" key="{{ $uuid }}-left-actions" />
                            </div>
                        </div>
                    </section>
                    <section class="before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-blue-500 before:to-blue-500/70 before:rounded-l-md bg-white/50">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-gray-900/10 border-r border-t border-b rounded-r">
                            <div class="flex items-center">
                                <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-blue-500/10 text-white rounded-br space-x-2">
                                    <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                    <span class="font-normal">{{ __('mailcoach - Branch') }}</span>
                                    B
                                </span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($rightActions) }} {{ trans_choice('mailcoach - action|actions', count($rightActions)) }}</span>
                                <button class="ml-auto -mr-8 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-right-actions" :automation="$automation" :actions="$rightActions" key="{{ $uuid}}-right-actions" />
                            </div>
                        </div>
                    </section>
                </div>
            @else
                <div class="grid gap-6 flex-grow">
                    <div class="grid gap-6 w-full">
                        <section class="before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-blue-500 before:to-blue-500/70 before:rounded-l-md bg-white/50">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-gray-900/10 border-r border-t border-b rounded-r">
                                <div class="flex items-center">
                                    <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-blue-500/10 text-white rounded-br space-x-2">
                                        <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                            <span class="font-normal">{{ __('mailcoach - Branch') }}</span>
                                            A
                                        </span>
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($leftActions) }} {{ trans_choice('mailcoach - action|actions', count($leftActions)) }}</span>
                                    <button class="ml-auto -mr-8 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div x-show="!collapsed">
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
                            </div>
                        </section>
                        <section class="before:content-[''] before:absolute before:w-2 before:h-full before:top-0 before:left-0 before:bg-gradient-to-b before:from-blue-500 before:to-blue-500/70 before:rounded-l-md bg-white/50">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-8': !collapsed }" class="grid gap-4 px-12 border-gray-900/10 border-r border-t border-b rounded-r">
                                <div class="flex items-center">
                                    <h2 class="justify-self-start -ml-10 -mt-px -mb-px h-8 px-2 inline-flex items-center bg-gray-900 bg-gradient-to-r from-blue-500/10 text-white rounded-br space-x-2">
                                        <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                        <span class="font-normal">{{ __('mailcoach - Branch') }}</span>
                                        B
                                    </span>
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-500 text-sm ml-4">{{ count($rightActions) }} {{ trans_choice('mailcoach - action|actions', count($rightActions)) }}</span>
                                    <button class="ml-auto -mr-8 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div x-show="!collapsed">
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
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
</x-mailcoach::fieldset>

