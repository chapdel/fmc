<?php

namespace Spatie\Mailcoach\Domain\Vendor\Postmark\Tests;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Vendor\Postmark\PostmarkSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Postmark\PostmarkWebhookConfig;
use Spatie\WebhookClient\WebhookConfig;

class PostmarkSignatureValidatorTest extends TestCase
{
    private WebhookConfig $config;

    private PostmarkSignatureValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = PostmarkWebhookConfig::get();

        $this->config->signingSecret = 'my-secret';

        $this->validator = new PostmarkSignatureValidator();
    }

    /** @test */
    public function it_requires_signature_data()
    {
        $request = new Request();

        $request->headers->set('mailcoach-signature', 'my-secret');

        $this->assertTrue($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_fails_if_signature_is_missing()
    {
        $request = new Request();

        $this->assertFalse($this->validator->isValid($request, $this->config));
    }

    /** @test * */
    public function it_fails_if_signature_is_invalid()
    {
        $request = new Request();

        $request->headers->set('mailcoach-signature', 'incorrect-secret');

        $this->assertFalse($this->validator->isValid($request, $this->config));
    }
}
