<div class="grid items-start justify-start gap-x-16 gap-y-8 md:grid-cols-[auto,auto]">
    @foreach (collect($availableConditions)->groupBy('category') as $category => $conditions)
        <div>
            <h4 class="mb-2 markup-h4">
                <x-mailcoach::rounded-icon size="md" minimal type="info" icon="fa-fw fas {{ \Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory::icons()[$category] }}" />

                {{ \Spatie\Mailcoach\Domain\ConditionBuilder\Enums\ConditionCategory::from($category)->label() }}
            </h4>
            <ul>
                @foreach ($conditions as $condition)
                    <li>
                        <a class="block link py-2 whitespace-nowrap" href="#" wire:click.prevent="add('{{ addslashes($condition['value']) }}')">
                            {{ $condition['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
