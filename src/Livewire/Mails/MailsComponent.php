<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Support\TimeZone;

class MailsComponent extends Component
{
    public function render()
    {
        $timeZones = TimeZone::all();

        return view('mailcoach::app.configuration.mails.edit', compact('timeZones'))
            ->layout('mailcoach::app.layouts.settings', ['title' => __mc('General')]);
    }
}
