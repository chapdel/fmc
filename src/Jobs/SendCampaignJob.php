<?php

namespace Spatie\Mailcoach\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Actions\Campaigns\SendCampaignAction;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Config;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    public int $tries = 1;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('mailcoach.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\SendCampaignAction $sendCampaignAction */
        $sendCampaignAction = Config::getActionClass('send_campaign', SendCampaignAction::class);

        $sendCampaignAction->execute($this->campaign);
    }
}
