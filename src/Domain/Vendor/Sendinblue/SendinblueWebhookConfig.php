<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue;

use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Jobs\ProcessSendinblueWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

class SendinblueWebhookConfig
{
    public static function get(): WebhookConfig
    {
        $config = config('mailcoach.sendinblue_feedback');

        return new WebhookConfig([
            'name' => 'sendinblue-feedback',
            'signing_secret' => $config['signing_secret'] ?? '',
            'header_name' => $config['header_name'] ?? 'Signature',
            'signature_validator' => $config['signature_validator'] ?? SendinblueSignatureValidator::class,
            'webhook_profile' => $config['webhook_profile'] ?? ProcessEverythingWebhookProfile::class,
            'webhook_model' => $config['webhook_model'] ?? WebhookCall::class,
            'process_webhook_job' => $config['process_webhook_job'] ?? ProcessSendinblueWebhookJob::class,
        ]);
    }
}
