<x-mailcoach::fieldset card class="p-0 gap-2">
    <x-slot name="legend">
        <header class="flex items-center text-sm space-x-2 py-2 px-4 bg-indigo-300/10 border-b border-indigo-700/10">
            <span class="font-normal normal-case tracking-normal">
                {{ $title }}
            </span>
        </header>
    </x-slot>
    <div class="flex items-center absolute top-2 right-4 gap-4 z-10">
        <button type="button" onclick="confirm('{{ __mc('Are you sure you want to delete this action?') }}') || event.stopImmediatePropagation()" wire:click="delete({{ $index }})">
            <i class="icon-button link-danger far fa-trash-alt"></i>
        </button>
    </div>
    <div class="form-actions mt-0 w-full px-2 md:px-4 py-1 mb-4">
        {{ $slot }}
    </div>
</x-mailcoach::fieldset>
