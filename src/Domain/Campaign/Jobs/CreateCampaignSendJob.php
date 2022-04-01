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
use Spatie\Mailcoach\Domain\Audience\Support\Segments\Segment;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class CreateCampaignSendJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public Campaign $campaign;

    public Subscriber $subscriber;

    public ?Segment $segment = null;

    public $tries = 1;

    /** @var string */
    public $queue;

    public function uniqueId(): string
    {
        return "{$this->campaign->id}{$this->subscriber->id}";
    }

    public function __construct(Campaign $campaign, Subscriber $subscriber, Segment $segment = null)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
        $this->segment = $segment;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_campaign_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        cache()->decrement("campaign-{$this->campaign->id}-sends-to-create");

        if ($this->segment && ! $this->segment->shouldSend($this->subscriber)) {
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

        $this->campaign->sends()->create([
            'subscriber_id' => $this->subscriber->id,
            'uuid' => (string) Str::uuid(),
        ]);
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
