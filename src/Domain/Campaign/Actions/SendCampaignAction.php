<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Jobs\MarkCampaignAsFullyDispatchedJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\MarkCampaignAsSentJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Support\Segments\Segment;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendCampaignAction
{
    public function execute(Campaign $campaign): void
    {
        if ($campaign->wasAlreadySent()) {
            return;
        }

        $this
            ->prepareSubject($campaign)
            ->prepareEmailHtml($campaign)
            ->prepareWebviewHtml($campaign)
            ->sendMailsForCampaign($campaign);
    }

    protected function prepareSubject(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Config::getActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($campaign);

        return $this;
    }

    protected function prepareEmailHtml(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Config::getActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($campaign);

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign): self
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Config::getActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($campaign);

        return $this;
    }

    protected function sendMailsForCampaign(Campaign $campaign): self
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        $campaign->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);

        $campaign->update(['all_jobs_added_to_batch_at' => null]);

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function () use ($campaign) {
                if (! $campaign->refresh()->all_jobs_added_to_batch_at) {
                    return $this;
                }

                dispatch(new MarkCampaignAsSentJob($campaign));
            })
            ->name($campaign->getBatchName())
            ->onQueue(config('mailcoach.perform_on_queue.send_mail_job'))
            ->dispatch();

        $campaign->update(['send_batch_id' => $batch->id]);

        $subscribersQuery
            ->cursor()
            ->map(fn (Subscriber $subscriber) => $this->createSendMailJob($campaign, $campaign->emailList, $subscriber, $segment))
            ->filter()
            ->chunk(1000)
            ->each(function (LazyCollection $jobs) use ($batch) {
                $batch->add($jobs);
            });

        $batch->add(new MarkCampaignAsFullyDispatchedJob($campaign));

        return $this;
    }

    protected function createSendMailJob(Campaign $campaign, EmailList $emailList, Subscriber $subscriber, Segment $segment = null): ?SendMailJob
    {
        if ($segment && ! $segment->shouldSend($subscriber)) {
            $campaign->decrement('sent_to_number_of_subscribers');

            return null;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $emailList)) {
            $campaign->decrement('sent_to_number_of_subscribers');

            return null;
        }

        $pendingSend = $this->createSend($campaign, $subscriber);

        return new SendMailJob($pendingSend);
    }

    protected function createSend(Campaign $campaign, Subscriber $subscriber): Send
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Send $pendingSend */
        $pendingSend = $campaign->sends()
            ->where('subscriber_id', $subscriber->id)
            ->first();

        if ($pendingSend) {
            return $pendingSend;
        }

        return $campaign->sends()->create([
            'subscriber_id' => $subscriber->id,
            'uuid' => (string)Str::uuid(),
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
