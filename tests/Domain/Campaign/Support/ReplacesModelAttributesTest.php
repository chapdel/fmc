<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns\ReplacesModelAttributes;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->classWithTrait = new class {
        use ReplacesModelAttributes;
    };
});

it('can replace model attributes', function () {
    $subscriber = Subscriber::factory()->create([
        'first_name' => 'John',
    ]);

    $output = test()->classWithTrait->replaceModelAttributes(
        "This is ::subscriber.first_name::",
        'subscriber',
        $subscriber
    );

    test()->assertEquals("This is John", $output);
});

it('will not thrown an exception when trying to replace an attribute with a null value', function () {
    $subscriber = Subscriber::factory()->create();

    $output = test()->classWithTrait->replaceModelAttributes(
        "This is ::subscriber.first_name::",
        'subscriber',
        $subscriber
    );

    test()->assertEquals("This is ", $output);
});

it('will not thrown an exception when trying to replace a non existing attribute', function () {
    $subscriber = Subscriber::factory()->create();

    $output = test()->classWithTrait->replaceModelAttributes(
        "This is ::subscriber.non_existing_attribute::",
        'subscriber',
        $subscriber
    );

    test()->assertEquals("This is ", $output);
});

it('will not thrown an exception when trying to replace a non existing schemaless attribute', function () {
    $subscriber = SubscriberFactory::new()->create();

    $output = test()->classWithTrait->replaceModelAttributes(
        "This is ::subscriber.extra_attributes.non_existing_attribute::",
        'subscriber',
        $subscriber
    );

    test()->assertEquals("This is ", $output);
});
