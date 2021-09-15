<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\AddTagsAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->action = new AddTagsAction(['some-tag', 'another-tag']);
});

it('continues after execution', function () {
    test()->assertTrue(test()->action->shouldContinue(test()->subscriber));
});

it('wont halt after execution', function () {
    test()->assertFalse(test()->action->shouldHalt(test()->subscriber));
});

it('adds tags to the subscriber', function () {
    test()->assertFalse(test()->subscriber->hasTag('some-tag'));
    test()->assertFalse(test()->subscriber->hasTag('another-tag'));

    test()->action->run(test()->subscriber);

    test()->assertTrue(test()->subscriber->hasTag('some-tag'));
    test()->assertTrue(test()->subscriber->hasTag('another-tag'));
});
