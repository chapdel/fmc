<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Jobs\MarkCampaignAsSentJob;
use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Support\Segments\Segment;

class SendCampaignAction
{
    public function execute(Campaign $campaign)
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

    protected function prepareSubject(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Config::getActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($campaign);

        return $this;
    }

    protected function prepareEmailHtml(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Config::getActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($campaign);

        return $this;
    }

    protected function prepareWebviewHtml(Campaign $campaign)
    {
        /** @var \Spatie\Mailcoach\Actions\Campaigns\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Config::getActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($campaign);

        return $this;
    }

    protected function sendMailsForCampaign(Campaign $campaign)
    {
        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        $subscribersQuery = $campaign->baseSubscribersQuery();

        $segment = $campaign->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        $campaign->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);

        $subscribersQuery->each(function (Subscriber $subscriber) use ($campaign, $segment) {
            $this->sendMail($campaign, $subscriber, $segment);
        });

        dispatch(new MarkCampaignAsSentJob($campaign));
    }

    protected function sendMail(Campaign $campaign, Subscriber $subscriber, Segment $segment): void
    {
        if (! $segment->shouldSend($subscriber)) {
            $campaign->decrement('sent_to_number_of_subscribers');

            return;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $campaign->emailList)) {
            $campaign->decrement('sent_to_number_of_subscribers');

            return;
        }

        $pendingSend = $this->createSend($campaign, $subscriber);

        dispatch(new SendMailJob($pendingSend));
    }

    protected function createSend(Campaign $campaign, Subscriber $subscriber): Send
    {
        /** @var \Spatie\Mailcoach\Models\Send $pendingSend */
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
