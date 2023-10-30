<div class="grid gap-4">
    @foreach ($storedConditions as $index => $storedCondition)
        @livewire(app(\Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction::class)->execute($storedCondition['condition']['key'])->getComponent(), [
            'index' => $index,
            'storedCondition' => $storedCondition,
            'emailList' => $emailList,
        ], key('stored-condition-' . $storedCondition['condition']['key'] . '-' . $index))
        @unless($loop->last)
            <div class="text-center uppercase tracking-wide text-xs">{{ __mc('And') }}</div>
        @endunless
    @endforeach

    @include('mailcoach::app.conditionBuilder.components.conditionsDropdown')
</div>
