<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Mails\Concerns\ReplacesPlaceholders;
use Spatie\Mailcoach\Models\Subscriber;

class ConfirmSubscriberMail extends Mailable implements ShouldQueue
{
    use ReplacesPlaceholders;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public string $confirmationUrl;

    public function __construct(Subscriber $subscriber, string $redirectAfterConfirmedUrl = '')
    {
        $this->subscriber = $subscriber;

        $this->confirmationUrl = url(route('mailcoach.confirm', $subscriber->uuid));

        if ($redirectAfterConfirmedUrl !== '') {
            $this->confirmationUrl .= "?redirect={$redirectAfterConfirmedUrl}";
        }
    }

    public function build()
    {
        $this
            ->from(
                $this->subscriber->emailList->default_from_email,
                $this->subscriber->emailList->default_from_name
            )
            ->determineSubject()
            ->determineContent();
    }

    protected function determineSubject(): self
    {
        $customSubject = $this->subscriber->emailList->confirmation_mail_subject;

        $subject = empty($customSubject)
            ? __('Confirm your subscription to :emailListName', ['emailListName' => $this->subscriber->emailList->name])
            : $this->replacePlaceholders($customSubject);

        $this->subject($subject);

        return $this;
    }

    protected function determineContent(): self
    {
        $customContent = $this->subscriber->emailList->confirmation_mail_content;

        if (! empty($customContent)) {
            $customContent = str_ireplace('::confirmUrl::', $this->confirmationUrl, $customContent);

            $customContent = $this->replacePlaceholders($customContent);

            $this->view('mailcoach::mails.customContent', ['content' => $customContent]);

            return $this;
        }

        $this->markdown('mailcoach::mails.confirmSubscription');

        return $this;
    }
}
