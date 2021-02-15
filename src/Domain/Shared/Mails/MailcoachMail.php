<?php

namespace Spatie\Mailcoach\Domain\Shared\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Swift_Message;

class MailcoachMail extends Mailable
{
    use SerializesModels;

    public ?Campaign $campaign = null;

    public ?Send $send = null;

    public string $htmlContent = '';

    public string $textContent = '';

    public ?string $fromEmail = null;

    public ?string $fromName = null;

    public ?string $replyToEmail = null;

    public ?string $replyToName = null;

    public $htmlView = null;

    public $textView = null;

    public function setSend(Send $send): self
    {
        $this->send = $send;

        $this->campaign = $send->campaign;

        return $this;
    }

    public function setFrom(string $fromEmail, string $fromName = null): self
    {
        $this->fromEmail = $fromEmail;

        $this->fromName = $fromName;

        return $this;
    }

    public function setReplyTo(string $replyToEmail, string $replyToName = null): self
    {
        $this->replyToEmail = $replyToEmail;

        $this->replyToName = $replyToName;

        return $this;
    }

    public function setHtmlView(string $htmlView): self
    {
        $this->htmlView = $htmlView;

        return $this;
    }

    public function setTextView(string $textView): self
    {
        $this->textView = $textView;

        return $this;
    }

    public function setCampaign(Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function setHtmlContent(string $htmlContent = ''): self
    {
        $this->htmlContent = $htmlContent;

        return $this;
    }

    public function setTextContent(string $textContent): self
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function subject($subject): self
    {
        if (! empty($this->subject)) {
            return $this;
        }

        $this->subject = $subject;

        return $this;
    }

    public function build()
    {
        $mail = $this
            ->from($this->fromEmail, $this->fromName)
            ->subject($this->subject)
            ->view($this->htmlView)
            ->text($this->textView)
            ->addUnsubscribeHeaders()
            ->storeTransportMessageId();

        if ($this->replyToEmail) {
            $mail->replyTo($this->replyToEmail, $this->replyToName);
        }

        return $mail;
    }

    protected function addUnsubscribeHeaders(): self
    {
        if (is_null($this->send)) {
            return $this;
        }

        $this->withSwiftMessage(function (Swift_Message $message) {
            $message
                ->getHeaders()
                ->addTextHeader(
                    'List-Unsubscribe',
                    '<' . $this->send->subscriber->unsubscribeUrl($this->send) . '>'
                );

            $message
                ->getHeaders()
                ->addTextHeader(
                    'List-Unsubscribe-Post',
                    'List-Unsubscribe=One-Click'
                );

            $message
                ->getHeaders()
                ->addTextHeader(
                    'mailcoach-send-uuid',
                    $this->send->uuid
                );
        });

        return $this;
    }

    protected function storeTransportMessageId(): self
    {
        if (is_null($this->send)) {
            return $this;
        }
        $this->withSwiftMessage(function (Swift_Message $message) {
            $this->send->storeTransportMessageId($message->getId());
        });

        return $this;
    }
}
