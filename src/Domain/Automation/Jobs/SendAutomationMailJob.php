<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendAutomationMailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public Send $pendingSend;

    /** @var string */
    public $queue;

    public function uniqueId(): string
    {
        return "{$this->pendingSend->id}";
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    public function __construct(Send $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('mailcoach.automation.perform_on_queue.send_automation_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Config::getAutomationActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware(): array
    {
        $rateLimitedMiddleware = (new RateLimited(useRedis: false))
            ->key('automation-mailer-throttle-' . config('mailcoach.automation.mailer') ?? config('mailcoach.mailer') ?? config('mail.default'))
            ->allow(config('mailcoach.automation.throttling.allowed_number_of_jobs_in_timespan'))
            ->everySeconds(config('mailcoach.automation.throttling.timespan_in_seconds'))
            ->releaseAfterOneSecond();

        return [$rateLimitedMiddleware];
    }
}
