<?php

namespace Spatie\Mailcoach\Livewire;

use Filament\Notifications\Notification;

trait LivewireFlash
{
    public function flashSuccess(string $message): self
    {
        $this->flash($message);

        return $this;
    }

    public function flashWarning(string $message): self
    {
        $this->flash($message, 'warning');

        return $this;
    }

    public function flashError(string $message): self
    {
        $this->flash($message, 'error');

        return $this;
    }

    public function flash(string $message, string $level = 'success')
    {
        Notification::make()
            ->title($message)
            ->color($level)
            ->send();
    }
}
