<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendgrid\Tests;

use Spatie\Mailcoach\Domain\Vendor\Sendgrid\SendgridSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\SendgridWebhookConfig;
use Spatie\WebhookClient\WebhookConfig;

class SendgridSignatureValidatorTest extends TestCase
{
    private WebhookConfig $config;

    private SendgridSignatureValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = SendgridWebhookConfig::get();

        $this->validator = new SendgridSignatureValidator();
    }

    /** @test */
    public function dummy_test()
    {
        $this->assertTrue(true);
    }
}
