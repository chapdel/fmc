<div class="flex justify-center items-center my-1 z-50">
    <x-mailcoach::dropdown direction="left">
        <x-slot name="trigger">
            <div class="button w-6 h-6 p-0 flex items-center justify-center rounded-full"><i class="far fa-plus text-xs"></i></div>
        </x-slot>

        @foreach ($actionOptions as $category => $actions)
        <h4 class="mb-2 px-6 markup-h4 text-gray-400 @unless($loop->first) mt-6 @endunless">
            <i class="fa-fw fas {{ \Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum::icons()[$category] }}"></i>
            {{ \Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum::make($category)->label }}
        </h4>
        <ul>
            @foreach ($actions as $action)
                <li>
                    <a href="#" wire:click.prevent="addAction('{{ addslashes($action) }}', {{ $index }})">
                        <span class="icon-label">
                            {{ $action::getName() }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
        @endforeach
    </x-mailcoach::dropdown>
</div>
