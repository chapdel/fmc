<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignTestAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignTestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var string */
    public $queue;

    public function __construct(public Campaign $campaign, public string $email, public ?ContentItem $contentItem = null)
    {
        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_test_mail_job');

        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignTestAction $sendCampaignTestAction */
        $sendCampaignTestAction = Mailcoach::getCampaignActionClass('send_test_mail', SendCampaignTestAction::class);

        $sendCampaignTestAction->execute($this->campaign, $this->email, $this->contentItem);
    }
}
