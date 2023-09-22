<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\EmailListFactory;

beforeEach(function () {
    test()->email_list = (new EmailListFactory())->create();
});

test('the configured campaign model extends package model', function () {
    $class = test()->email_list->getCampaignClass();
    test()->assertInstanceOf(Campaign::class, new $class);
});

test('the configured email list model extends package model', function () {
    $class = test()->email_list->getEmailListClass();
    test()->assertInstanceOf(EmailList::class, new $class);
});

test('the configured subscriber model extends package model', function () {
    $class = test()->email_list->getSubscriberClass();
    test()->assertInstanceOf(Subscriber::class, new $class);
});

test('the configured template model extends package model', function () {
    $class = test()->email_list->getTemplateClass();
    test()->assertInstanceOf(Template::class, new $class);
});

test('the configured send model extends package model', function () {
    $class = test()->email_list->getSendClass();
    test()->assertInstanceOf(Send::class, new $class);
});
