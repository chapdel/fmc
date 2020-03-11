<?php

namespace Spatie\Mailcoach\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Actions\Subscribers\ImportSubscribersAction;
use Spatie\Mailcoach\Models\SubscriberImport;
use Spatie\Mailcoach\Support\Config;

class ImportSubscribersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public SubscriberImport $subscriberImport;

    public ?User $user;

    public function __construct(SubscriberImport $subscriberImport, User $user = null)
    {
        $this->subscriberImport = $subscriberImport;

        $this->user = $user;
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Subscribers\ImportSubscribersAction $importSubscribersAction */
        $importSubscribersAction = Config::getActionClass('import_subscribers', ImportSubscribersAction::class);

        $importSubscribersAction->execute($this->subscriberImport, $this->user);
    }
}
