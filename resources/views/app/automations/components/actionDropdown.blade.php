<div class="flex justify-center items-center my-1">
    <x-mailcoach::dropdown direction="left">
        <x-slot name="trigger">
            <div class="button w-6 h-6 p-0 flex items-center justify-center rounded-full"><i class="far fa-plus text-xs"></i></button>
        </x-slot>
        
        @foreach ($actionOptions as $actionClass => $actionName)
            <ul>
                <li>
                    <a href="#" wire:click.prevent="addAction('{{ addslashes($actionClass) }}', {{ $index }})">
                        <span class="icon-label">
                            {{ $actionName }}
                        </span>
                    </a>
                </li>
            </ul>
        @endforeach
    </x-mailcoach::dropdown>
</div>
