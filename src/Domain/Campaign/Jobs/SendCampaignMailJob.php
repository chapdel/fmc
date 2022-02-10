<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendCampaignMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public Send $pendingSend;

    public $tries = 3;

    /** @var string */
    public $queue;

    public function __construct(Send $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if ($this->pendingSend->campaign->isCancelled()) {
            if (! $this->pendingSend->wasAlreadySent()) {
                $this->pendingSend->delete();
            }

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Config::getCampaignActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }
}
