<?php

namespace Spatie\Mailcoach\Http\App\Livewire\ConditionBuilder;

use Livewire\Component;
use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class ConditionComponent extends Component
{
    use UsesMailcoachModels;

    public array $storedCondition;

    public int $index = 0;

    public string $title;

    public function mount(): void
    {
        $this->title = app(CreateConditionFromKeyAction::class)
            ->execute($this->storedCondition['condition']['key'])
            ->label();
    }

    public function updated(): void
    {
        $this->emit('storedConditionUpdated', $this->index, $this->storedCondition);
    }

    public function delete(): void
    {
        $this->emit('storedConditionDeleted', $this->index);
    }
}
