<x-mailcoach::fieldset card class="md:p-6" :focus="$editing" wire:init="loadData">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold counter-automation">
                {{ $index + 1 }}
            </span>
            <span class="font-normal">
                {{ $legend ?? $action['class']::getName() }}
            </span>
        </header>
    </x-slot>

    <div class="flex items-center absolute top-4 right-6 gap-4 z-10">
        @if ($editing)
            <button type="button" wire:key="save-{{ $index }}" wire:click="save" class="hover:text-green-500">
                <i class="icon-button fas fa-check"></i>
                Save
            </button>
        @elseif ($editable)
            <button type="button" wire:key="edit-{{ $index }}" wire:click="edit">
                <i class="icon-button far fa-edit"></i>
            </button>
        @endif
        @if ($deletable)
            <x-mailcoach::confirm-button :confirm-text="__mc('Are you sure you want to delete this action?')" on-confirm="() => $wire.delete()">
                <i class="icon-button link-danger far fa-trash-alt"></i>
            </x-mailcoach::confirm-button>
        @endif
    </div>

    @if ($editing)
        <div class="form-actions">
            {{ $form ?? '' }}
        </div>
    @else
        @if(! empty(trim($content ?? '')))
            <div>
                {{ $content }}
            </div>
        @endif

        <dl class="-mb-6 -mx-6 px-6 py-2 flex items-center justify-end text-xs bg-indigo-300/10 border-t border-indigo-700/10">
            <span>
                Active
                <span wire:loading.remove wire:target="loadData" class="font-semibold variant-numeric-tabular">{{ isset($action['active']) ? number_format($action['active']) : '...' }}</span>
                <span wire:loading wire:target="loadData" class="font-semibold variant-numeric-tabular">&hellip;</span>
            </span>
            <span class="text-gray-400 px-2">•</span>
            <span>
                Completed
                <span wire:loading.remove wire:target="loadData" class="font-semibold variant-numeric-tabular">{{ isset($action['completed']) ? number_format($action['completed']) : '...' }}</span>
                <span wire:loading wire:target="loadData" class="font-semibold variant-numeric-tabular">&hellip;</span>
            </span>
            <span>
                <span class="text-gray-400 px-2">•</span>
                Halted
                <span wire:loading.remove wire:target="loadData" class="font-semibold variant-numeric-tabular">{{ isset($action['halted']) ? number_format($action['halted']) : '...' }}</span>
                <span wire:loading wire:target="loadData" class="font-semibold variant-numeric-tabular">&hellip;</span>
            </span>
        </dl>
    @endif
</x-mailcoach::fieldset>
