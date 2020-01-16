<?php

namespace Spatie\Mailcoach\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Actions\Campaigns\ConvertHtmlToTextAction;
use Spatie\Mailcoach\Actions\Campaigns\PersonalizeHtmlAction;
use Spatie\Mailcoach\Actions\Campaigns\SendCampaignAction;
use Spatie\Mailcoach\Actions\Campaigns\SendMailAction;
use Spatie\Mailcoach\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Support\Config;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public Send $pendingSend;

    /** @var string */
    public $queue;

    public function __construct(Send $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('mailcoach.perform_on_queue.send_mail_job');
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\SendMailAction $sendMailAction */
        $sendMailAction = Config::getActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware()
    {
        $throttlingConfig = config('mailcoach.throttling');

        $rateLimitedMiddleware = (new RateLimited())
            ->enabled($throttlingConfig['enabled'])
            ->connectionName($throttlingConfig['redis_connection_name'])
            ->allow($throttlingConfig['allowed_number_of_jobs_in_timespan'])
            ->everySeconds($throttlingConfig['timespan_in_seconds'])
            ->releaseAfterSeconds($throttlingConfig['release_in_seconds']);

        return [$rateLimitedMiddleware];
    }

    public function retryUntil()
    {
        return now()->addDay();
    }
}
