<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendCampaignMailJob implements ShouldQueue, ShouldBeUnique
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

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_mail_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        if ($this->pendingSend->campaign->isCancelled()) {
            if (! $this->pendingSend->wasAlreadySent()) {
                $this->pendingSend->delete();
            }

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Mailcoach::getCampaignActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware(): array
    {
        $mailer = $this->pendingSend->campaign->getMailerKey();

        $rateLimitedMiddleware = (new RateLimited(useRedis: false))
            ->key($mailer)
            ->allow(config("mail.mailers.{$mailer}.mails_per_timespan", 10))
            ->everySeconds(config("mail.mailers.{$mailer}.timespan_in_seconds", 1))
            ->releaseAfterOneSecond();

        return [$rateLimitedMiddleware];
    }
}
