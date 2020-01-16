<?php

namespace Spatie\Mailcoach\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Actions\Campaigns\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Actions\Campaigns\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Actions\Campaigns\RetrySendingFailedSendsAction;
use Spatie\Mailcoach\Actions\Campaigns\SendCampaignAction;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Events\CampaignSentEvent;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Support\Segments\Segment;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('mailcoach.perform_on_queue.send_campaign_job');
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\SendCampaignAction $sendCampaignAction */
        $sendCampaignAction = Config::getActionClass('send_campaign', SendCampaignAction::class);

        $sendCampaignAction->execute($this->campaign);
    }
}
