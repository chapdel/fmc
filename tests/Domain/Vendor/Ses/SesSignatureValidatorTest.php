<?php

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Vendor\Ses\SesSignatureValidator;
use Spatie\Mailcoach\Domain\Vendor\Ses\SesWebhookConfig;

beforeEach(function () {
    $this->config = SesWebhookConfig::get();

    $this->validator = new SesSignatureValidator();
});

function validParams(array $overrides = []): array
{
    return array_merge($this->getStub('bounceWebhookContent'), $overrides);
}

it('requires signature data', function () {
    $request = Request::create('/ses-feedback', 'POST', [], [], [], [], json_encode(validParams()));

    $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'] = 'SubscriptionConfirmation';

    expect($this->validator->isValid($request, $this->config))->toBeTrue();
});

/** @test * */
function it_calls_the_subscribe_url_when_its_a_subscription_confirmation_requests()
{
    $params = $this->getStub('subscriptionConfirmation');
    $params['SubscribeURL'] = url('test-route');

    $request = Request::create('/ses-feedback', 'POST', [], [], [], [], json_encode($params));

    $this->expectExceptionMessage('file_get_contents('.url('test-route').')');

    $this->validator->isValid($request, $this->config);
}
