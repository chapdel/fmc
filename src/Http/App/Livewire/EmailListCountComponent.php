<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class EmailListCountComponent extends Component
{
    public ?string $result = null;

    public bool $readyToLoad = false;

    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function load()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        if ($this->readyToLoad) {
            $this->result = $this->emailList->subscribers()->count();
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
