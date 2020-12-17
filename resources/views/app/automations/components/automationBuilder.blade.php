<div>
    <input type="hidden" name="actions" value="{{ json_encode($actions) }}">
    @foreach ($actions as $index => $action)
        @if($loop->first)
            @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index])
        @endif
        <div class="border-2 border-blue-200 p-4 mb-4 relative">
            <div>{{ $index + 1 }}. {{ $action['class']::getName() }}</div>
            <div class="flex items-center absolute top-0 right-0 mt-4 mr-4 gap-2">
                @if ($action['editing'] ?? false)
                    <button class="text-blue-400" type="button" wire:click="saveAction({{ $index }})">
                        <i class="fas fa-save"></i>
                    </button>
                @elseif (collect($actions)->where('editing', true)->count() === 0)
                    <button class="text-blue-400" type="button" wire:click="editAction({{ $index }})">
                        <i class="fas fa-edit"></i>
                    </button>
                @endif
                <button class="text-red-400" type="button" wire:click="removeAction({{ $index }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            @if ($action['editing'] ?? false && $action['class']::getComponent())
                <div class="mt-6">
                    @livewire($action['class']::getComponent(), [
                        'actionClass' => $action['class'],
                        'automation' => $automation,
                        'actionData' => $action['data'] ?? [],
                    ], key($index))
                </div>
            @else
                <div>{!! $action['class']::make($action['data'])->getDescription() !!}</div>
            @endif
        </div>
        @unless($loop->last)
            @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
        @endunless
    @endforeach

    @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
</div>
