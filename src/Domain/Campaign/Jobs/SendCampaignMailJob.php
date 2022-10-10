<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
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
        return $this->pendingSend->id;
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    public function __construct(Send $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        $campaign = $this->pendingSend->campaign;

        if ($campaign->isCancelled()) {
            if (! $this->pendingSend->wasAlreadySent()) {
                $this->pendingSend->delete();
            }

            return;
        }

        $subscriber = $this->pendingSend->subscriber;

        if (! $campaign->getSegment()->shouldSend($subscriber)) {
            $campaign->decrement('sent_to_number_of_subscribers');
            $this->pendingSend->delete();

            return;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $campaign->emailList)) {
            $campaign->decrement('sent_to_number_of_subscribers');
            $this->pendingSend->delete();

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Config::getCampaignActionClass('send_mail', SendMailAction::class);
        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware(): array
    {
        if ($this->pendingSend->campaign->isCancelled()) {
            return [];
        }

        $rateLimitedMiddleware = (new RateLimited(useRedis: false))
            ->key('mailer-throttle-' . (config('mailcoach.campaigns.mailer') ?? config('mailcoach.mailer') ?? config('mail.default')))
            ->allow(config('mailcoach.campaigns.throttling.allowed_number_of_jobs_in_timespan'))
            ->everySeconds(config('mailcoach.campaigns.throttling.timespan_in_seconds'))
            ->releaseAfterOneSecond();

        return [$rateLimitedMiddleware];
    }

    protected function isValidSubscriptionForEmailList(Subscriber $subscriber, EmailList $emailList): bool
    {
        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if ((int)$subscriber->email_list_id !== (int)$emailList->id) {
            return false;
        }

        return true;
    }
}
