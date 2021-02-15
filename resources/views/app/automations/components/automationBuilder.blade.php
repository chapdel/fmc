<div>
    <input type="hidden" name="actions" value="{{ json_encode($actions) }}">
    @foreach ($actions as $index => $action)
        @if($loop->first)
            @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index])
        @endif
        
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

            <div class="flex items-center absolute top-4 right-4 space-x-3">
                @if ($action['class']::getComponent())
                    @if ($action['editing'] ?? false)
                        <button type="button" wire:click="saveAction({{ $index }})">
                            <i class="icon-button hover:text-green-500 fas fa-check"></i>
                        </button>
                    @elseif (collect($actions)->where('editing', true)->count() === 0)
                        <button type="button" wire:click="editAction({{ $index }})">
                            <i class="icon-button far fa-edit"></i>
                        </button>
                    @endif
                @endif
                <button type="button" wire:click="removeAction({{ $index }})">
                    <i class="icon-button hover:text-red-500 far fa-trash-alt"></i>
                </button>
            </div>

            <div>
                @if ($action['editing'] ?? false && $action['class']::getComponent())
                    <div class="mb-4">
                        @livewire($action['class']::getComponent(), [
                            'automation' => $automation,
                            'componentData' => $action['data'] ?? [],
                        ], key($index))
                    </div>
                @else
                    <div class="tag-neutral">{!! $action['class']::make($action['data'])->getDescription() !!}</div>
                @endif
                
                <dl class="mt-4 dl text-xs">
                    <dt>Active</dt> 
                    <dd>{{ $action['active'] ?? 0 }}</dd>
                    <dt>Completed</dt> 
                    <dd>{{ $action['completed'] ?? 0 }}</dd>
                </dl>
            </div>
        </x-mailcoach::fieldset>

        @unless($loop->last)
            @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
        @endunless
    @endforeach

    @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
</div>
