<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

trait LivewireFlash
{
    public function flash(string $message, string $level = 'success')
    {
        $this->dispatchBrowserEvent('notify', [
            'content' => $message,
            'type' => $level,
        ]);
    }
}
