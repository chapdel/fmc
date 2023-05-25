<?php

namespace Spatie\Mailcoach\Domain\Settings\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;

class WebhookDisabledMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $theme = 'mailcoach::mails.layout.mailcoach';

    private string|array $recipients;

    public WebhookConfiguration $webhookConfiguration;

    public function __construct(string|array $recipients, WebhookConfiguration $webhookConfiguration)
    {
        $this->recipients = $recipients;
        $this->webhookConfiguration = $webhookConfiguration;
    }

    public function build()
    {
        return $this
            ->subject(__mc("Mailcoach Webhook {$this->webhookConfiguration->name} disabled"))
            ->to($this->recipients)
            ->markdown('mailcoach::mails.webhookDisabled');
    }
}
