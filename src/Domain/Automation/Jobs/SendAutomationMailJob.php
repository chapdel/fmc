<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendAutomationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AutomationMail $automationMail;

    public int $tries = 1;

    /** @var string */
    public $queue;

    public function __construct(AutomationMail $automationMail)
    {
        $this->automationMail = $automationMail;

        $this->queue = config('mailcoach.automation.perform_on_queue.send_automation_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendCampaignAction $sendCampaignAction */
        $sendCampaignAction = Config::getCampaignActionClass('send_campaign', SendCampaignAction::class);

        $sendCampaignAction->execute($this->automationMail);
    }
}
