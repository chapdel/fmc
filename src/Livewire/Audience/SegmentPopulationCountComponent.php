<?php

namespace Spatie\Mailcoach\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class SegmentPopulationCountComponent extends Component
{
    public ?string $result = null;

    public bool $readyToLoad = false;

    public TagSegment $segment;

    protected $listeners = [
        'segmentUpdated' => '$refresh',
    ];

    public function mount(TagSegment $segment)
    {
        $this->segment = $segment;
    }

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        if ($this->readyToLoad) {
            $this->result = $this->segment->getSubscribersQuery()->count();
        }

        return <<<'blade'
            <span wire:init="load" title="{{ number_format($result) }}">
                @if ($readyToLoad)
                {{ Str::shortNumber($result) }}
                @else
                ...
                @endif
            </span>
        blade;
    }
}
