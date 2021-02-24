<x-mailcoach::fieldset>
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold bg-yellow-200 text-yellow-600 shadow">
                {{ $index + 1 }}
            </span>
            <span>
                {{ $action['class']::getName() }}
            </span>
        </header>
    </x-slot>

    <div class="relative">
        <div class="flex items-center absolute top-4 right-4 space-x-3 z-20">
            @if ($editing)
                <button type="button" wire:click="save">
                    <i class="icon-button hover:text-green-500 fas fa-check"></i>
                </button>
            @elseif ($editable)
                <button type="button" wire:click="edit">
                    <i class="icon-button far fa-edit"></i>
                </button>
            @endif
            @if ($deletable)
                <button type="button" wire:click="delete">
                    <i class="icon-button hover:text-red-500 far fa-trash-alt"></i>
                </button>
            @endif
        </div>

        <div class="relative z-10">
            @if ($editing)
                <div class="mb-4">
                    <div>
                        {{ $form ?? '' }}
                    </div>
                </div>
            @else
                {{ $content ?? '' }}

                <dl class="mt-4 dl text-xs">
                    <dt>Active</dt>
                    <dd>{{ $action['active'] ?? 0 }}</dd>
                    <dt>Completed</dt>
                    <dd>{{ $action['completed'] ?? 0 }}</dd>
                </dl>
            @endif
        </div>
    </div>
</x-mailcoach::fieldset>
