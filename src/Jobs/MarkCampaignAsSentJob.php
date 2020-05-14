<?php

namespace Spatie\Mailcoach\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Config;

class MarkCampaignAsSentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    /** @var string */
    public $queue;

    /** We will retry this on each minute for an entire day */
    public int $tries = 60 * 24;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('mailcoach.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if (! $this->allMailsHaveBeenSent()) {
            $this->release(60);

            return;
        }

        $this->campaign->markAsSent($this->campaign->sends()->count());

        event(new CampaignSentEvent($this->campaign));
    }

    protected function allMailsHaveBeenSent(): bool
    {
        return (int) $this->campaign->sendsCount() === (int) $this->campaign->fresh()->sent_to_number_of_subscribers;
    }
}
