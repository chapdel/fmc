<?php

namespace Spatie\Mailcoach\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Send;
use Swift_Message;

class CampaignMail extends Mailable
{
    use SerializesModels;

    public ?Campaign $campaign = null;

    public ?Send $send = null;

    public string $htmlContent = '';

    public string $textContent = '';

    public function setSend(Send $send)
    {
        $this->send = $send;

        $this->campaign = $send->campaign;

        return $this;
    }

    public function setCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function setHtmlContent(string $htmlContent = '')
    {
        $this->htmlContent = $htmlContent ?? '';

        return $this;
    }

    public function setTextContent(string $textContent)
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
        return $this
            ->from(
                $this->campaign->from_email ?? $this->campaign->emailList->default_from_email,
                $this->campaign->from_name ?? $this->campaign->emailList->default_from_name ?? null
            )
            ->subject($this->subject)
            ->view('mailcoach::mails.campaignHtml')
            ->text('mailcoach::mails.campaignText')
            ->addUnsubscribeHeaders()
            ->storeTransportMessageId();
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
