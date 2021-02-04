<div class="flex justify-center items-center my-6">
    <x-mailcoach::dropdown direction="left">
        <x-slot name="trigger">
            <i class="far fa-plus | block text-2xl icon-button"></i>
        <x-slot>
        
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
