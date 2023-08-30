<?php

namespace Spatie\Mailcoach\Livewire\Mails;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\TableComponent;

class SuppressionListComponent extends TableComponent
{
    use UsesMailcoachModels;

    public function getTableQuery(): Builder
    {
        return self::getSubscriberClass()::query();
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
