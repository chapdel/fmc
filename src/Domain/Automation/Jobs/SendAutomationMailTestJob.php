<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailTestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var string */
    public $queue;

    public function __construct(public AutomationMail $mail, public string $email, public ?ContentItem $contentItem = null)
    {
        $this->queue = config('mailcoach.automation.perform_on_queue.send_test_mail_job');

        $this->connection ??= Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendAutomationMailTestAction $sendTestMailAction */
        $sendTestMailAction = Mailcoach::getAutomationActionClass('send_test_mail', SendAutomationMailTestAction::class);

        $sendTestMailAction->execute($this->mail, $this->email, $this->contentItem);
    }
}
