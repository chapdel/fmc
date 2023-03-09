<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;

class TagPopulationCountComponent extends Component
{
    public ?string $result = null;

    public bool $readyToLoad = false;

    public Tag $tag;

    protected $listeners = [
        'segmentUpdated' => '$refresh',
    ];

    public function mount(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        if ($this->readyToLoad) {
            $this->result = $this->tag->subscribers()->subscribed()->count();
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
