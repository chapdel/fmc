<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, Subscriber $subscriber): void
    {
        if ($automationMail->wasAlreadySentToSubscriber($subscriber)) {
            return;
        }

        if (! $subscriber) {
            return;
        }

        $this
            ->prepareSubject($automationMail)
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->sendMail($automationMail, $subscriber);
    }

    protected function prepareSubject(AutomationMail $automationMail): self
    {
        /** @var \Spatie\Mailcoach\Domain\AutomationMail\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Config::getAutomationMailActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($automationMail);

        return $this;
    }

    protected function prepareEmailHtml(AutomationMail $automationMail): self
    {
        /** @var \Spatie\Mailcoach\Domain\AutomationMail\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Config::getAutomationMailActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($automationMail);

        return $this;
    }

    protected function prepareWebviewHtml(AutomationMail $automationMail): self
    {
        /** @var \Spatie\Mailcoach\Domain\AutomationMail\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Config::getAutomationMailActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($automationMail);

        return $this;
    }

    protected function sendMailsForAutomationMail(AutomationMail $automationMail): self
    {
        $automationMail->update(['segment_description' => $automationMail->getSegment()->description()]);

        $subscribersQuery = $automationMail->baseSubscribersQuery();

        $segment = $automationMail->getSegment();

        $segment->subscribersQuery($subscribersQuery);

        $automationMail->update(['sent_to_number_of_subscribers' => $subscribersQuery->count()]);

        $automationMail->update(['all_jobs_added_to_batch_at' => null]);

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function () use ($automationMail) {
                if (! $automationMail->refresh()->all_jobs_added_to_batch_at) {
                    return $this;
                }

                dispatch(new MarkAutomationMailAsSentJob($automationMail));
            })
            ->name($automationMail->getBatchName())
            ->onQueue(config('mailcoach.automationMails.perform_on_queue.send_mail_job'))
            ->dispatch();

        $automationMail->update(['send_batch_id' => $batch->id]);

        $subscribersQuery
            ->cursor()
            ->map(fn (Subscriber $subscriber) => $this->createSendMailJob($automationMail, $automationMail->emailList, $subscriber, $segment))
            ->filter()
            ->chunk(1000)
            ->each(function (LazyCollection $jobs) use ($batch) {
                $batch->add($jobs);
            });

        $batch->add(new MarkAutomationMailAsFullyDispatchedJob($automationMail));

        return $this;
    }

    protected function createSendMailJob(AutomationMail $automationMail, EmailList $emailList, Subscriber $subscriber, Segment $segment = null): ?SendMailJob
    {
        if ($segment && ! $segment->shouldSend($subscriber)) {
            $automationMail->decrement('sent_to_number_of_subscribers');

            return null;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $emailList)) {
            $automationMail->decrement('sent_to_number_of_subscribers');

            return null;
        }

        $pendingSend = $this->createSend($automationMail, $subscriber);

        return new SendMailJob($pendingSend);
    }

    protected function createSend(AutomationMail $automationMail, Subscriber $subscriber): Send
    {
        /** @var \Spatie\Mailcoach\Domain\AutomationMail\Models\Send $pendingSend */
        $pendingSend = $automationMail->sends()
            ->where('subscriber_id', $subscriber->id)
            ->first();

        if ($pendingSend) {
            return $pendingSend;
        }

        return $automationMail->sends()->create([
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

    protected function sendMail(AutomationMail $automationMail, Subscriber $subscriber)
    {
        $automationMail->update(['segment_description' => $automationMail->getSegment()->description()]);

        dispatch($this->createSendMailJob($automationMail, $subscriber->emailList, $subscriber));
    }
}
