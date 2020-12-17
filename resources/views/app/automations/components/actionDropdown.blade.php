<div class="flex justify-center items-center my-6">
    <div class="dropdown" data-dropdown>
        <button type="button" class="dropdown-trigger" data-dropdown-trigger>
            <i class="fas fa-plus | block text-2xl icon-button"></i>
        </button>
        <ul class="dropdown-list w-56 dropdown-list-left | hidden" data-dropdown-list>
            @foreach ($actionOptions as $actionClass => $actionName)
                <li>
                    <a href="#" wire:click.prevent="addAction('{{ addslashes($actionClass) }}', {{ $index }})">
                        <span class="icon-label">
                            {{ $actionName }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
