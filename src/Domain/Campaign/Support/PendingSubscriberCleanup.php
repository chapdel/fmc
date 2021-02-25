<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\RetrieveInactiveSubscribersAction;
use Spatie\Mailcoach\Domain\Campaign\Mails\ReconfirmationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\InactiveSubscriber;

class PendingSubscriberCleanup
{
    public EmailList $emailList;

    public ?int $didNotOpenPastNumberOfCampaigns = null;

    public ?int $didNotClickPastNumberOfCampaigns = null;

    public CarbonInterval $unsubscribeAfter;

    public string $redirectAfterReconfirmation = '';

    public string $subject;

    public function __construct(EmailList $emailList)
    {
        $this->emailList = $emailList;
        $this->subject = (string) __('We’ve missed you! Take action to continue receiving our emails!');
    }

    public function didNotOpenPastNumberOfCampaigns(int $numberOfCampaigns): self
    {
        $this->didNotOpenPastNumberOfCampaigns = $numberOfCampaigns;

        return $this;
    }

    public function didNotClickPastNumberOfCampaigns(int $numberOfCampaigns): self
    {
        $this->didNotClickPastNumberOfCampaigns = $numberOfCampaigns;

        return $this;
    }

    public function unsubscribeAfter(CarbonInterval $interval): self
    {
        $this->unsubscribeAfter = $interval;

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function redirectAfterReconfirmation(string $url): self
    {
        $this->redirectAfterReconfirmation = $url;

        return $this;
    }

    public function sendReconfirmationMail(): void
    {
        $query = resolve(RetrieveInactiveSubscribersAction::class)->execute(
            $this->emailList,
            $this->didNotOpenPastNumberOfCampaigns,
            $this->didNotClickPastNumberOfCampaigns,
        );

        $query->each(function (Subscriber $subscriber) {
            $mail = (new ReconfirmationMail($subscriber, $this->redirectAfterReconfirmation))
                ->build()
                ->to($subscriber->email)
                ->subject($this->subject);

            Mail::send($mail);

            InactiveSubscriber::create([
                'subscriber_id' => $subscriber->id,
                'unsubscribe_at' => now()->add($this->unsubscribeAfter),
            ]);
        });
    }
}
