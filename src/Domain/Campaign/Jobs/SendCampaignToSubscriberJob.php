<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignToSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendCampaignToSubscriberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    public Subscriber $subscriber;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign, Subscriber $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignToSubscriberAction $sendCampaignToSubscriberAction */
        $sendCampaignToSubscriberAction = Config::getCampaignActionClass('send_campaign_to_subscriber', SendCampaignToSubscriberAction::class);
        $sendCampaignToSubscriberAction->execute($this->campaign, $this->subscriber);
    }
}
