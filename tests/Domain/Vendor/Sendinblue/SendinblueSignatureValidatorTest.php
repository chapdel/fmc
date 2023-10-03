<?php

namespace Spatie\Mailcoach\Domain\Vendor\Sendinblue\Exceptions\Domain\Vendor\Sendinblue\Actions\Tests;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Exceptions\Domain\Vendor\Sendinblue\Actions\SendinblueSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Exceptions\Domain\Vendor\Sendinblue\Actions\SendinblueWebhookConfig;
use Spatie\WebhookClient\WebhookConfig;

class SendinblueSignatureValidatorTest extends TestCase
{
    private WebhookConfig $config;

    private SendinblueSignatureValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('mailcoach.sendinblue_feedback.signing_secret', '1234');
        $this->config = SendinblueWebhookConfig::get();

        $this->validator = new SendinblueSignatureValidator();
    }

    /** @test */
    public function it_requires_a_valid_secret()
    {
        $request = Request::create('/?secret=1234');
        $request2 = Request::create('/?secret=123');

        $this->assertTrue($this->validator->isValid($request, $this->config));
        $this->assertFalse($this->validator->isValid($request2, $this->config));
    }
}
