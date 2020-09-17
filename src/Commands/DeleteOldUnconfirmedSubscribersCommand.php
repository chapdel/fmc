<?php

namespace Spatie\Mailcoach\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;
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

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Config::getActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deletedSubscribersCount = $this->getSubscriberClass()::unconfirmed()
            ->where('created_at', '<', $cutOffDate)
            ->each(function (Subscriber $subscriber) use ($deleteSubscriberAction) {
                $deleteSubscriberAction->execute($subscriber);
            });

        $this->comment("Deleted {$deletedSubscribersCount} unconfirmed subscribers.");
    }
}
