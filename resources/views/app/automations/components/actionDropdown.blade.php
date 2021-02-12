<div class="flex justify-center items-center my-1">
    <x-mailcoach::dropdown direction="left">
        <x-slot name="trigger">
            <div class="button w-6 h-6 p-0 flex items-center justify-center rounded-full"><i class="far fa-plus text-xs"></i></button>
        </x-slot>
        
        <h4 class="mb-2 px-6 markup-h4 text-gray-400">
            <i class="fa-fw fas fa-tag"></i>
            Tag
        </h4>
        <ul>
            @foreach ($actionOptions as $actionClass => $actionName)
                    @if(str_contains($actionName, 'tag'))
                    <li>
                        <a href="#" wire:click.prevent="addAction('{{ addslashes($actionClass) }}', {{ $index }})">
                            <span class="icon-label">
                                {{ $actionName }}
                            </span>
                        </a>
                    </li>
                    @endif
            @endforeach
        </ul>

        <h4 class="mt-6 mb-2 px-6 markup-h4 text-gray-400">
            <i class="fa-fw far fa-clock"></i>
            Pause
        </h4>
        <ul>
            @foreach ($actionOptions as $actionClass => $actionName)
                     @if(str_contains($actionName, 'duration') || str_contains($actionName, 'Halt'))
                    <li>
                        <a href="#" wire:click.prevent="addAction('{{ addslashes($actionClass) }}', {{ $index }})">
                            <span class="icon-label">
                                {{ $actionName }}
                            </span>
                        </a>
                    </li>
                    @endif
            @endforeach
        </ul>

        <h4 class="mt-6 mb-2 px-6 markup-h4 text-gray-400">
            <i class="fa-fw fas fa-random"></i>
            React
        </h4>
        <ul>
            @foreach ($actionOptions as $actionClass => $actionName)
                     @if($actionName == 'Unsubscribe' || str_contains($actionName, 'Send'))
                    <li>
                        <a href="#" wire:click.prevent="addAction('{{ addslashes($actionClass) }}', {{ $index }})">
                            <span class="icon-label">
                                {{ $actionName }}
                            </span>
                        </a>
                    </li>
                    @endif
            @endforeach
        </ul>
    </x-mailcoach::dropdown>
</div>
