<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class DeleteOldUnconfirmedSubscribersCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:delete-old-unconfirmed-subscribers';

    public $description = 'Delete all unsubscribed subscribers';

    public function handle()
    {
        $this->comment('Deleting old unconfirmed subscribers...');

        $cutOffDate = now()->subMonth()->toDateTimeString();

        $deletedSubscribersCount = $this->getSubscriberClass()::unconfirmed()->where('created_at', '<', $cutOffDate)->delete();

        $this->comment("Deleted {$deletedSubscribersCount} unconfirmed subscribers.");
    }
}
