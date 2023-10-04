<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class SendinblueSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        return $request->get('secret') === $config->signingSecret;
    }
}
