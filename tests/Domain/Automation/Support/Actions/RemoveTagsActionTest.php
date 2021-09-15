<?php

use Spatie\Mailcoach\Domain\Automation\Support\Actions\RemoveTagsAction;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->subscriber = SubscriberFactory::new()->confirmed()->create();
    test()->action = new RemoveTagsAction(['some-tag', 'another-tag']);
});

it('continues after execution', function () {
    expect(test()->action->shouldContinue(test()->subscriber))->toBeTrue();
});

it('wont halt after execution', function () {
    expect(test()->action->shouldHalt(test()->subscriber))->toBeFalse();
});

it('removes a tag from the subscriber', function () {
    test()->subscriber->addTag('some-tag');
    test()->subscriber->addTag('another-tag');

    expect(test()->subscriber->hasTag('some-tag'))->toBeTrue();
    expect(test()->subscriber->hasTag('another-tag'))->toBeTrue();

    test()->action->run(test()->subscriber);

    expect(test()->subscriber->hasTag('some-tag'))->toBeFalse();
    expect(test()->subscriber->hasTag('another-tag'))->toBeFalse();
});
