<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendTestMailAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendTestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    public string $email;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign, string $email)
    {
        $this->campaign = $campaign;

        $this->email = $email;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_test_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendTestMailAction $sendTestMailAction */
        $sendTestMailAction = Config::getCampaignActionClass('send_test_mail', SendTestMailAction::class);

        $sendTestMailAction->execute($this->campaign, $this->email);
    }
}
