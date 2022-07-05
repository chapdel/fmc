<?php

namespace Spatie\Mailcoach\Domain\Audience\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Mailcoach;

class ImportSubscriberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public SubscriberImport $subscriberImport;

    public array $values;

    public function __construct(SubscriberImport $subscriberImport, array $values)
    {
        $this->subscriberImport = $subscriberImport;

        $this->values = $values;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.import_subscribers_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ImportSubscriberAction $importSubscriberAction */
        $importSubscriberAction = Mailcoach::getAudienceActionClass('import_subscriber', ImportSubscriberAction::class);

        $importSubscriberAction->execute($this->subscriberImport, $this->values);
    }
}
