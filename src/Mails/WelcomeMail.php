<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Mails\Concerns\ReplacesPlaceholders;
use Spatie\Mailcoach\Models\Subscriber;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use ReplacesPlaceholders;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
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
        $customSubject = $this->subscriber->emailList->welcome_mail_subject;

        $subject = empty($customSubject)
            ? __('Welcome to :emailListName', ['emailListName' => $this->subscriber->emailList->name])
            : $this->replacePlaceholders($customSubject);

        $this->subject($subject);

        return $this;
    }

    protected function determineContent(): self
    {
        $customContent = $this->subscriber->emailList->welcome_mail_content;

        if (! empty($customContent)) {
            $customContent = $this->replacePlaceholders($customContent);

            $customContent = str_ireplace('::unsubscribeUrl::', $this->subscriber->unsubscribeUrl(), $customContent);

            $this->view('mailcoach::mails.customContent', ['content' => $customContent]);

            return $this;
        }

        $this->markdown('mailcoach::mails.welcome');

        return $this;
    }
}
