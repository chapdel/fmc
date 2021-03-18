<div class="flex my-6">
    <div class="w-64 -ml-6 pl-6 h-0 flex justify-start items-center border-t border-dashed border-gray-500 border-opacity-25">
    <x-mailcoach::dropdown direction="right">
        <x-slot name="trigger">
                <div class="button-rounded" title="{{__('Insert action')}}">+</div>
        </x-slot>

        <div style="min-width:40rem" class="max-w-full px-1 py-4 grid items-start gap-8 grid-cols-2">
            @foreach ($actionOptions as $category => $actions)
            <div>
                <h4 class="mb-2 px-6 markup-h4 text-yellow-700">
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
            </div>
            @endforeach
        </div>
    </x-mailcoach::dropdown>
    </div>
</div>
