<?php

namespace Spatie\Mailcoach\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;

class TagPopulationCountComponent extends Component
{
    public ?string $result = null;

    protected $listeners = [
        'segmentUpdated' => '$refresh',
    ];

    public function mount(Tag $tag)
    {
        $this->result = $tag->subscribers()->subscribed()->count();
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <span>…</span>
        HTML;
    }

    public function render()
    {
        return <<<'blade'
            <span title="{{ number_format($result) }}">
                {{ Str::shortNumber($result) }}
            </span>
        blade;
    }
}
