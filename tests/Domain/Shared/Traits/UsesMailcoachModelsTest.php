<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Tests\Factories\EmailListFactory;

beforeEach(function () {
    test()->email_list = (new EmailListFactory())->create();
});

test('the configured campaign model extends package model', function () {
    $class = test()->email_list->getCampaignClass();
    expect(new $class)->toBeInstanceOf(Campaign::class);
});

test('the configured email list model extends package model', function () {
    $class = test()->email_list->getEmailListClass();
    expect(new $class)->toBeInstanceOf(EmailList::class);
});

test('the configured subscriber model extends package model', function () {
    $class = test()->email_list->getSubscriberClass();
    expect(new $class)->toBeInstanceOf(Subscriber::class);
});

test('the configured template model extends package model', function () {
    $class = test()->email_list->getTemplateClass();
    expect(new $class)->toBeInstanceOf(Template::class);
});

test('the configured send model extends package model', function () {
    $class = test()->email_list->getSendClass();
    expect(new $class)->toBeInstanceOf(Send::class);
});
