<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Rules\EmailListSubscriptionRule;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();

    test()->rule = new EmailListSubscriptionRule(test()->emailList);
});

it('will not pass if the given email is already subscribed', function () {
    test()->assertTrue(test()->rule->passes('email', 'john@example.com'));
    test()->emailList->subscribe('john@example.com');
    test()->assertFalse(test()->rule->passes('email', 'john@example.com'));

    $otherEmailList = EmailList::factory()->create();
    $rule = new EmailListSubscriptionRule($otherEmailList);
    test()->assertTrue($rule->passes('email', 'john@example.com'));
});

it('will pass for emails that are still pending', function () {
    test()->emailList->update(['requires_confirmation' => true]);
    test()->emailList->subscribe('john@example.com');
    test()->assertEquals(SubscriptionStatus::UNCONFIRMED, test()->emailList->getSubscriptionStatus('john@example.com'));

    test()->assertTrue(test()->rule->passes('email', 'john@example.com'));
});

it('will pass for emails that are unsubscribed', function () {
    test()->emailList->update(['requires_confirmation' => true]);
    test()->emailList->subscribe('john@example.com');
    test()->emailList->unsubscribe('john@example.com');
    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, test()->emailList->getSubscriptionStatus('john@example.com'));

    test()->assertTrue(test()->rule->passes('email', 'john@example.com'));
});

it('will allow to subscribe an email that is already subscribed to another list', function () {
    test()->emailList->subscribe('john@example.com');

    $anotherEmailList = EmailList::factory()->create();

    test()->assertTrue((new EmailListSubscriptionRule($anotherEmailList))->passes('email', 'john@example.com'));
});
