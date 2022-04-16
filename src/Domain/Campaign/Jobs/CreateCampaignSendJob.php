<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Support\Throttling\SimpleThrottle;

class CreateCampaignSendJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Campaign $campaign;

    protected Subscriber $subscriber;

    public $tries = 1;

    /** @var string */
    public $queue;

    public function uniqueId(): string
    {
        return "{$this->campaign->id}{$this->subscriber->id}";
    }

    public function __construct(Campaign $campaign, Subscriber $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if ($this->campaign->isCancelled()) {
            return;
        }

        if (! $this->campaign->getSegment()->shouldSend($this->subscriber)) {
            $this->campaign->decrement('sent_to_number_of_subscribers');

            return;
        }

        if (! $this->isValidSubscriptionForEmailList($this->subscriber, $this->campaign->emailList)) {
            $this->campaign->decrement('sent_to_number_of_subscribers');

            return;
        }

        $pendingSend = $this->campaign->sends()
            ->where('subscriber_id', $this->subscriber->id)
            ->exists();

        if ($pendingSend) {
            return;
        }

        $send = $this->campaign->sends()->create([
            'subscriber_id' => $this->subscriber->id,
            'uuid' => (string) Str::uuid(),
        ]);

        $this->dispatchMailSendJob($send);
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

    private function dispatchMailSendJob(Send $send): void
    {
        $simpleThrottle = app(SimpleThrottle::class)
            ->forMailer(config('mailcoach.campaigns.mailer'))
            ->allow(config('mailcoach.campaigns.throttling.allowed_number_of_jobs_in_timespan'))
            ->inSeconds(config('mailcoach.campaigns.throttling.timespan_in_seconds'));

        $simpleThrottle->hit();

        dispatch(new SendCampaignMailJob($send));

        $send->markAsSendingJobDispatched();
    }
}
