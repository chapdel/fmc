<?php

namespace Spatie\Mailcoach\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Actions\Campaigns\SendTestMailAction;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Config;

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

        $this->queue = config('mailcoach.perform_on_queue.send_test_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\SendTestMailAction $sendTestMailAction */
        $sendTestMailAction = Config::getActionClass('send_test_mail', SendTestMailAction::class);

        $sendTestMailAction->execute($this->campaign, $this->email);
    }
}
