<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Livewire\Component;

class SuppressionListComponent extends Component
{
    public function mount()
    {
        dd('is it found?');
    }

    public function render()
    {
        $suppressions = [
            [
                'email' => 'niels@spatire.com',
                'stream' => 'transactional',
                'Reason' => 'Unknown',
            ],
        ];

        return view('mailcoach::app.configuration.mails.partials.suppression-list', [
            'suppressions' => $suppressions,
            'totalSuppressionsCount' => 1,
        ])->layout('mailcoach::app.layouts.settings', ['title' => __mc('Suppression List')]);
    }
}
