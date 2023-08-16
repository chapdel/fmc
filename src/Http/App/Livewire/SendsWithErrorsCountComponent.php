<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class SendsWithErrorsCountComponent extends Component
{
    public ?string $result = null;

    public bool $readyToLoad = false;

    public Sendable $sendable;

    public function mount(Sendable $sendable)
    {
        $this->sendable = $sendable;
    }

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        if ($this->readyToLoad) {
            $this->result = $this->sendable->sendsWithErrors()->count();
        }

        return <<<'blade'
            <span wire:init="load">
                @if ($readyToLoad && $result)
                    <div class="flex items-center text-orange-500 text-xs mt-1">
                        <x-mailcoach::rounded-icon type="warning" icon="fas fa-info" class="mr-1" />
                        {{ $result }} {{ __mc_choice('failed send|failed sends', $result) }}
                    </div>
                @endif
            </span>
        blade;
    }
}
