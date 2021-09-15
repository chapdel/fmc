<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\RemoveTagsAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->action = new RemoveTagsAction(['some-tag', 'another-tag']);
});

it('continues after execution', function () {
    test()->assertTrue(test()->action->shouldContinue(test()->subscriber));
});

it('wont halt after execution', function () {
    test()->assertFalse(test()->action->shouldHalt(test()->subscriber));
});

it('removes a tag from the subscriber', function () {
    test()->subscriber->addTag('some-tag');
    test()->subscriber->addTag('another-tag');

    test()->assertTrue(test()->subscriber->hasTag('some-tag'));
    test()->assertTrue(test()->subscriber->hasTag('another-tag'));

    test()->action->run(test()->subscriber);

    test()->assertFalse(test()->subscriber->hasTag('some-tag'));
    test()->assertFalse(test()->subscriber->hasTag('another-tag'));
});
